import React from "react";
import GroupIcon from "@mui/icons-material/Group";
import MapsUgcIcon from "@mui/icons-material/MapsUgc";

const PointInformation = ({
  isOpen,
  membershipTier = "Silver",
  points = 100,
  redeemValue = 100,
}) => {
  if (!isOpen) return null;

  return (
    <section className="point-info__container">
      <div className="point-info__title">
        <label htmlFor="epos_crm_billing_point">
          Membership Point Information
        </label>
      </div>
      <div className="point-info__group">
        <GroupIcon className="point-info__icon" />
        <div className="point-info__detail">
          <label htmlFor="epos_crm_billing_point">Membership Tier: </label>
          <span className="point-info__value">{membershipTier}</span>
        </div>
      </div>
      <div className="point-info__group">
        <MapsUgcIcon className="point-info__icon" />
        <div className="point-info__detail">
          <label htmlFor="epos_crm_billing_point">
            <strong>{points} Point(s) </strong>
          </label>
          <span> available to redeem</span>
          <label className="point-info__value"> ~ ${redeemValue}</label>
        </div>
      </div>
      <div className="point-info__group">
        <input
          type="text"
          className="input-text "
          name="epos_crm_billing_point"
          id="epos_crm_billing_point"
          placeholder="First Name"
        ></input>
      </div>
    </section>
  );
};

export default PointInformation;
