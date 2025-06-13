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
            helperText={error?.message}
          />
        )}
      />
    </div>
  );
};

export default InputField;
