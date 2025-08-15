import React from "react";
import { Controller } from "react-hook-form";
import { TextField, Typography } from "@mui/material";

const InputField = ({
  label,
  name,
  control,
  error,
  required,
  maxRows,
  multiline,
  ...inputProps
}) => {
  return (
    <div>
      {label && (
        <Typography className="input-label" gutterBottom>
          {label}
          {required && <span style={{ color: "#CC0000" }}> *</span>}
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
            multiline={multiline}
            maxRows={1}
            size="small"
            error={!!error}
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

export default InputField;
