import React from "react";
import { Controller } from "react-hook-form";
import {
  FormControl,
  InputAdornment,
  OutlinedInput,
  Typography,
} from "@mui/material";
import MonetizationOnIcon from "@mui/icons-material/MonetizationOn";

const PointField = ({ label, name, control, error, ...inputProps }) => {
  return (
    <div className="input-point">
      <Controller
        name={name}
        control={control}
        defaultValue=""
        render={({ field }) => (
          <FormControl fullWidth variant="outlined" size="small">
            {label && (
              <Typography className="input-label" gutterBottom>
                {label}
                {<span style={{ color: "#CC0000" }}> *</span>}
              </Typography>
            )}
            <OutlinedInput
              {...field}
              type="number"
              startAdornment={
                <InputAdornment position="start">
                  <MonetizationOnIcon />
                </InputAdornment>
              }
              error={!!error}
              {...inputProps}
            />
          </FormControl>
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

export default PointField;
