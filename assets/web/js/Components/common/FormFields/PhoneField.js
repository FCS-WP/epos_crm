import React, { useRef } from "react";
import { Controller } from "react-hook-form";
import { MuiTelInput } from "mui-tel-input";
import { Typography } from "@mui/material";

const PhoneField = ({ label, name, control, error, ...inputProps }) => {
  return (
    <div>
      {label && (
        <Typography className="input-label" gutterBottom>
          {label}
          {<span style={{ color: "#CC0000" }}> *</span>}
        </Typography>
      )}
      <Controller
        name={name}
        control={control}
        defaultValue=""
        render={({ field: { ref: fieldRef, ...fieldProps } }) => (
          <MuiTelInput
            {...fieldProps}
            {...inputProps}
            inputRef={fieldRef}
            autoComplete="phone"
            defaultCountry="SG"
            fullWidth
            size="small"
            error={!!error}
            className="epos-phone-field"
          />
        )}
      />
       {error && (
        <Typography variant="body2" color="error" className="epos-error-msg">
          {error.message}
        </Typography>
      )}
    </div>
  );
};

export default PhoneField;
