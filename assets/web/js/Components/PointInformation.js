import React, { useState, useEffect, useRef, useCallback } from "react";
import * as yup from "yup";
import { yupResolver } from "@hookform/resolvers/yup";
import { useForm } from "react-hook-form";
import GroupIcon from "@mui/icons-material/Group";
import MapsUgcIcon from "@mui/icons-material/MapsUgc";
import { CircularProgress } from "@mui/material";
import PointField from "./common/FormFields/PointField";
import Toast from "./common/notifications/toast";
import { webApi } from "../api";

const PointInformation = ({
  isOpen,
  membershipTier = "Silver",
  points = 0,
  pointRate = 0,
  cartTotal = 0,
  currentPoints = 0,
}) => {
  if (!isOpen) return null;

  const [pointBalance, setPointBalance] = useState(currentPoints);
  const [pointRateState, setPointRate] = useState(pointRate);
  const [loading, setLoading] = useState(false);
  const debounceTimer = useRef(null);

  const convertPoint = useCallback((points, rate) => points * rate, []);
  const covertToPoint = useCallback((cost, rate) => cost / rate, []);

  const pointSchema = yup.object().shape({
    point: yup
      .number()
      .typeError("Point must be a number")
      .min(1, "You must redeem at least 1 point")
      .test("max-points-tiered", "", function (value) {
        const { createError } = this;

        if (!value) return true;

        if (value > cartTotal) {
          return createError({
            message: "You've entered more points value than your order total",
          });
        }

        if (value > convertPoint(pointBalance, pointRateState)) {
          return createError({
            message:
              "You've entered more points than the points you currently can redeem",
          });
        }
        return true;
      }),
  });

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
      point: currentPoints > 0 ? currentPoints : null,
    },
  });

  const enteredPoint = watch("point");

  // API call for redeem
  const onSubmit = useCallback(
    async (data) => {
      if (loading) return;

      setLoading(true);
      try {
        const pointData = {
          is_used: true,
          point_used: data.point,
          points: covertToPoint(Number(data.point), pointRateState),
        };

        const { data: response } = await webApi.pointRedeem(pointData);

        if (response?.status === "success") {
          document.body.dispatchEvent(
            new Event("update_checkout", { bubbles: true })
          );
          const submitBtn = document.getElementById("place_order");
          if (submitBtn) submitBtn.disabled = false;
        } else {
          Toast({
            method: "error",
            subtitle:
              response?.errors || "Failed to redeem point. Please try again.",
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
    },
    [loading, pointRateState]
  );

  // Fetch customer data
  const fetchCustomerData = useCallback(async () => {
    if (loading) return;
    setLoading(true);

    try {
      const { data: response } = await webApi.getCustomer();

      if (response?.status === "success") {
        setPointBalance(response.data.point_balance);
        setPointRate(response.data.point_conversion_rate);
      } else {
        Toast({
          method: "error",
          subtitle:
            response?.errors || "Failed to fetch data point. Please try again.",
        });
      }
    } catch (err) {
      Toast({
        method: "error",
        subtitle: "An error occurred while fetching data point",
      });
    } finally {
      setLoading(false);
    }
  }, []);

  // Fetch customer data when the first load or points change
  useEffect(() => {
    if (points > 0) {
      fetchCustomerData();
    }
    setPointBalance(convertPoint(points, pointRateState));
    reset({ point: currentPoints > 0 ? currentPoints : null });
  }, [
    points,
    currentPoints,
    pointRateState,
    fetchCustomerData,
    convertPoint,
    ,
  ]);

  // Debounced auto-submit
  useEffect(() => {
    const submitBtn = document.getElementById("place_order");

    if (!enteredPoint || !isValid) {
      if (submitBtn) submitBtn.disabled = true;
      return;
    }

    if (debounceTimer.current) clearTimeout(debounceTimer.current);

    debounceTimer.current = setTimeout(() => {
      handleSubmit(onSubmit)();
    }, 1500);

    return () => {
      if (debounceTimer.current) clearTimeout(debounceTimer.current);
    };
  }, [enteredPoint, isValid, handleSubmit]);

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
            ~ ${convertPoint(points, pointRateState)}
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
        {loading && (
          <div className="point-redeem-button">
            <CircularProgress size={16} />
          </div>
        )}
      </div>

      {enteredPoint && isValid && (
        <div className="point-info__group points_to_redeem">
          <div className="point-info__detail">
            <label>
              <strong>Points to redeem: </strong>
            </label>
            <span>
              ${enteredPoint} ~{" "}
              {covertToPoint(Number(enteredPoint), pointRateState)} point(s)
            </span>
          </div>
        </div>
      )}
    </form>
  );
};

export default PointInformation;
