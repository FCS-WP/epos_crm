import React from "react";
import { Controller } from "react-hook-form";
import { TextField, Typography } from "@mui/material";

const InputField = ({ label, name, control, error, ...inputProps }) => {
  return (
    <div>
      {label && (
        <Typography className="input-label" gutterBottom>
          {label}
        </Typography>
      )}
      <Controller
        name={name}
        control={control}
        defaultValue=""
        render={({ field }) => (
          <TextField
            {...field}
            {...inputProps}
            autoComplete="off"
            fullWidth
            size="small"
            error={!!error}
            helperText={error?.message}
          />
        )}
      />
    </div>
  );
};

export default InputField;
