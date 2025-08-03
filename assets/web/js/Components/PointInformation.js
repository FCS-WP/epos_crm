import React, {
  useState,
  useEffect,
  useRef,
  useCallback,
  useMemo,
} from "react";
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
  redeemableLimit,
  isRedeemableLimit,
}) => {
  if (!isOpen) return null;

  const [pointBalance, setPointBalance] = useState(currentPoints);
  const [pointRateState, setPointRate] = useState(pointRate);
  const [loading, setLoading] = useState(false);
  const debounceTimer = useRef(null);

  const convertPoint = useCallback((points, rate) => points * rate, []);
  const convertToPoint = useCallback((cost, rate) => cost / rate, []);

  // ✅ Validation schema (memoized)
  const pointSchema = useMemo(
    () =>
      yup.object().shape({
        point: yup
          .number()
          .typeError("Point must be a number")
          .min(0, "You must redeem at least 0 point")
          .test("max-points-tiered", "", function (value) {
            const { createError } = this;
            if (!value) return true;

            if (value > cartTotal) {
              return createError({
                message:
                  "You've entered more points value than your order total",
              });
            }

            const maxRedeemable = isRedeemableLimit
              ? redeemableLimit
              : convertPoint(pointBalance, pointRateState);

            if (value > maxRedeemable) {
              return createError({
                message:
                  "You've entered more points than the points you currently can redeem",
              });
            }

            return true;
          }),
      }),
    [
      cartTotal,
      isRedeemableLimit,
      redeemableLimit,
      pointBalance,
      pointRateState,
      convertPoint,
    ]
  );

  const {
    control,
    handleSubmit,
    formState: { errors, isValid },
    watch,
    reset,
  } = useForm({
    resolver: yupResolver(pointSchema),
    mode: "onChange",
    defaultValues: { point: currentPoints || null },
  });

  const enteredPoint = watch("point");

  // ✅ Redeem handler
  const onSubmit = useCallback(
    async ({ point }) => {
      if (loading) return;

      setLoading(true);
      try {
        const pointData = {
          is_used: true,
          point_used: point,
          points: convertToPoint(Number(point), pointRateState),
        };

        const { data: response } = await webApi.pointRedeem(pointData);

        if (response?.status === "success") {
          document.body.dispatchEvent(
            new Event("update_checkout", { bubbles: true })
          );
          document.getElementById("place_order")?.removeAttribute("disabled");
        } else {
          Toast({
            method: "error",
            subtitle:
              response?.errors || "Failed to redeem point. Please try again.",
          });
        }
      } catch {
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
          subtitle: response?.errors || "Failed to fetch points data",
        });
      }
    } catch {
      Toast({
        method: "error",
        subtitle: "An error occurred while fetching points",
      });
    } finally {
      setLoading(false);
    }
  }, []);

  // Initial load
  useEffect(() => {
    if (points > 0) fetchCustomerData();
    reset({ point: currentPoints || null });
  }, [points, currentPoints, fetchCustomerData]);

  // Debounced auto-submit
  useEffect(() => {
    if (!enteredPoint || !isValid) return;

    if (debounceTimer.current) clearTimeout(debounceTimer.current);

    debounceTimer.current = setTimeout(() => handleSubmit(onSubmit)(), 1500);

    return () => {
      if (debounceTimer.current) clearTimeout(debounceTimer.current);
    };
  }, [enteredPoint, isValid, handleSubmit]);

  // Disable/enable place order button
  useEffect(() => {
    const btn = document.getElementById("place_order");
    if (btn) btn.disabled = !enteredPoint || !isValid;
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
            ~ $
            {isRedeemableLimit
              ? redeemableLimit
              : convertPoint(points, pointRateState)}
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
              {convertToPoint(Number(enteredPoint), pointRateState)} point(s)
            </span>
          </div>
        </div>
      )}
    </form>
  );
};

export default PointInformation;
