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
}) => {
  if (!isOpen) return null;

  const [point, setPoint] = useState(0);

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

        if (value > points) {
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
      point: "",
    },
  });

  const convertPoint = (points, rate) => {
    return points * rate;
  };

  const onSubmit = async (data) => {
    if (loading) return;

    setLoading(true);

    const pointData = {
      is_used: true,
      point_used: data.point,
    };
    try {
      const { data } = await webApi.pointRedeem(pointData);

      if (data && data?.status == "success") {
        Toast({
          method: "success",
          subtitle: `You have redeemed ${pointData.point_used} point(s) successfully.`,
        });

        // reset();

        const updateEvent = new Event("update_checkout", { bubbles: true });
        document.body.dispatchEvent(updateEvent);
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
  }, [points]);

  const debounceTimer = useRef(null);

  useEffect(() => {
    if (!enteredPoint || !isValid) return;

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
          <span> available to redeem</span>
          <label className="point-info__value">
            {" "}
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
    </form>
  );
};

export default PointInformation;
