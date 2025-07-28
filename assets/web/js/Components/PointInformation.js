import React, { useState, useEffect, useRef } from "react";
import * as yup from "yup";
import { yupResolver } from "@hookform/resolvers/yup";
import { useForm } from "react-hook-form";
import GroupIcon from "@mui/icons-material/Group";
import MapsUgcIcon from "@mui/icons-material/MapsUgc";
import { Button, CircularProgress } from "@mui/material";
import PointField from "./common/FormFields/PointField";
import Toast from "./common/notifications/toast";
import { webApi } from "../api";

const PointInformation = ({
  isOpen,
  membershipTier = "Silver",
  points = 0,
  pointRate = 0,
  cartTotal = 0,
  currentPoints,
}) => {
  if (!isOpen) return null;

  const [point, setPoint] = useState(currentPoints);

  const pointSchema = yup.object().shape({
    point: yup

      .number()
      .typeError("Point must be a number")
      .min(1, "You must redeem at least 1 point")
      .test("max-points-tiered", "", function (value) {
        const { createError } = this;

        if (value > cartTotal) {
          return createError({
            message: "You've entered more points value than your order total",
          });
        }

        if (value > convertPoint(points, pointRate)) {
          return createError({
            message:
              "You've entered more points than the points you currently can redeem",
          });
        }
        return true;
      }),
  });
  const [loading, setLoading] = useState(false);

  const {
    control,
    handleSubmit,
    formState: { errors, isValid },
    watch,
    reset,
  } = useForm({
    resolver: yupResolver(pointSchema),
    mode: "onChange",
    defaultValues: {
      point: currentPoints,
    },
  });

  const convertPoint = (points, rate) => {
    return points * rate;
  };

  const covertToPoint = (cost, rate) => {
    return cost / rate;
  };

  const onSubmit = async (data) => {
    if (loading) return;

    setLoading(true);

    const pointData = {
      is_used: true,
      point_used: data.point,
      points: covertToPoint(Number(data.point), pointRate),
    };
    try {
      const { data } = await webApi.pointRedeem(pointData);

      if (data && data?.status == "success") {
        const updateEvent = new Event("update_checkout", { bubbles: true });
        document.body.dispatchEvent(updateEvent);

        const submitBtn = document.getElementById("place_order");
        if (submitBtn) submitBtn.disabled = false;
      } else {
        const errorMessage =
          data?.errors || "Failed to redeem point. Please try again.";
        Toast({
          method: "error",
          subtitle: errorMessage,
        });
      }
    } catch (err) {
      Toast({
        method: "error",
        subtitle: "An error occurred while redeeming point",
      });
    } finally {
      setLoading(false);
    }
  };

  

  const enteredPoint = watch("point");

  useEffect(() => {
    const point = convertPoint(points, pointRate);
    setPoint(point);
  }, [points, currentPoints]);

  const debounceTimer = useRef(null);

  useEffect(() => {
    if (!enteredPoint || !isValid) {
      const submitBtn = document.getElementById("place_order");
      if (submitBtn) submitBtn.disabled = true;
      return;
    }

    clearTimeout(debounceTimer.current);

    debounceTimer.current = setTimeout(() => {
      handleSubmit(onSubmit)();
    }, 1500);

    return () => clearTimeout(debounceTimer.current);
  }, [enteredPoint, isValid]);

  return (
    <form
      className="point-info__container"
      onSubmit={handleSubmit(onSubmit)}
      noValidate
    >
      <div className="point-info__title">
        <label htmlFor="epos_crm_billing_point">
          Membership Point Information
        </label>
      </div>

      <div className="point-info__group">
        <GroupIcon className="point-info__icon" />
        <div className="point-info__detail">
          <label>Membership Tier: </label>
          <span className="point-info__value">{membershipTier}</span>
        </div>
      </div>

      <div className="point-info__group">
        <MapsUgcIcon className="point-info__icon" />
        <div className="point-info__detail">
          <label>
            <strong>{points} Point(s)</strong>
          </label>
          <span> available to redeem </span>
          <label className="point-info__value">
            ~ ${convertPoint(points, pointRate)}
          </label>
        </div>
      </div>

      <div className="point-info__group group_input">
        <PointField
          type="number"
          className="input-text epos_crm_point_input"
          name="point"
          id="epos_crm_billing_point"
          placeholder="Enter points to redeem"
          control={control}
          error={errors.point}
        />
        <div className="point-redeem-button">
          {loading && <CircularProgress size={16} />}
        </div>
      </div>
      {enteredPoint && isValid && (
        <div className="point-info__group points_to_redeem">
          <div className="point-info__detail">
            <label>
              <strong>Points to redeem: </strong>
            </label>
            <span>
              ${enteredPoint} ~ {covertToPoint(Number(enteredPoint), pointRate)}
              point(s)
            </span>
          </div>
        </div>
      )}
    </form>
  );
};

export default PointInformation;
