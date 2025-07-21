import React, { useState } from "react";
import * as yup from "yup";
import { yupResolver } from "@hookform/resolvers/yup";
import { useForm } from "react-hook-form";
import GroupIcon from "@mui/icons-material/Group";
import MapsUgcIcon from "@mui/icons-material/MapsUgc";
import { Button, CircularProgress } from "@mui/material";
import PointField from "./common/FormFields/PointField";
import { webApi } from "../api";

const pointSchema = yup.object().shape({
  point: yup
    .number()
    .typeError("Point must be a number")
    .min(1, "You must redeem at least 1 point")
    .required("Please enter a point value"),
});

const PointInformation = ({
  isOpen,
  membershipTier = "Silver",
  points = 100,
  pointRate = 100,
}) => {
  if (!isOpen) return null;

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

  const convertPoint = (point, rate) => {
    return (point * rate) / 100;
  };

  const onSubmit = async (data) => {
    if (loading) return;
    setLoading(true);
    try {
      const { data } = await webApi.pointRedeem();

      if (data && data?.status == "success") {
        Toast({
          method: "success",
          subtitle: "successfully.",
        });

        reset();
        handleClosePopup();
        window.location.reload();
      } else {
        const errorMessage = data?.errors || "Failed to update email";
        Toast({
          method: "error",
          subtitle: errorMessage,
        });
      }
    } catch (err) {
      Toast({
        method: "error",
        subtitle: "An error occurred while updating your email",
      });
    } finally {
      setLoading(false);
    }
  };

  const enteredPoint = watch("point");

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

        <Button
          disabled={!isValid || loading || !enteredPoint}
          variant="contained"
          type="submit"
          className="point-redeem-button"
          startIcon={loading && <CircularProgress size={16} />}
        >
          {loading ? "Applying..." : "Apply"}
        </Button>
      </div>
    </form>
  );
};

export default PointInformation;
