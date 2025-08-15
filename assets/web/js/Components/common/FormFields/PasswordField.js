import React, { useState } from "react";
import { Controller } from "react-hook-form";
import {
  FormControl,
  InputAdornment,
  IconButton,
  OutlinedInput,
  Typography,
  Tooltip,
} from "@mui/material";
import { Visibility, VisibilityOff, InfoRounded } from "@mui/icons-material";
import PasswordInfoTooltip from "../notifications/PasswordInfoTooltip";
const PasswordField = ({
  label,
  name,
  control,
  error,
  tootip,
  tenant,
  ...inputProps
}) => {
  const [showPassword, setShowPassword] = useState(false);

  const togglePasswordVisibility = () => setShowPassword((prev) => !prev);

  return (
    <div style={{ marginBottom: "1rem" }}>
      <Controller
        name={name}
        control={control}
        defaultValue=""
        render={({ field }) => (
          <FormControl fullWidth variant="outlined" size="small">
            {label && (
              <Typography className="input-label" gutterBottom>
                {label}{" "}
                {tootip && (
                  <Tooltip
                    className="epos-tooltip"
                    title={<PasswordInfoTooltip tenant={tenant} />}
                    arrow
                    sx={{ backgroundColor: "#fff", border: "1px solid #ccc" }}
                  >
                    <IconButton size="small">
                      <InfoRounded />
                    </IconButton>
                  </Tooltip>
                )}
                {<span style={{ color: "#CC0000" }}> *</span>}
              </Typography>
            )}
            <OutlinedInput
              {...field}
              type={showPassword ? "text" : "password"}
              autoComplete="current-password"
              endAdornment={
                <InputAdornment position="end">
                  <IconButton
                    onClick={togglePasswordVisibility}
                    edge="end"
                    size="small"
                    className="hide-password"
                    aria-label={
                      showPassword ? "Hide password" : "Show password"
                    }
                  >
                    {showPassword ? <VisibilityOff /> : <Visibility />}
                  </IconButton>
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

export default PasswordField;
