import React from "react";
import { Controller } from "react-hook-form";
import { Typography, TextField, Autocomplete } from "@mui/material";

const SelectCountryField = ({
  label,
  name,
  control,
  error,
  data,
  defaultValue,
  ...inputProps
}) => {
  return (
    <div>
      {label && (
        <Typography className="input-label" gutterBottom>
          {label}
          <span style={{ color: "#CC0000" }}> *</span>
        </Typography>
      )}
      <Controller
        name={name}
        control={control}
        defaultValue={defaultValue}
        render={({ field: { ref, onChange, value, ...rest } }) => {
          const selectedOption =
            data.find((item) => item.code === value) || null;

          return (
            <Autocomplete
              {...inputProps}
              {...rest}
              options={data}
              value={selectedOption}
              className="epos-country-field"
              getOptionLabel={(option) => option.name || ""}
              isOptionEqualToValue={(option, val) => option.code === val.code}
              onChange={(_, selected) =>
                onChange(selected ? selected.code : "")
              }
              renderInput={(params) => (
                <TextField
                  {...params}
                  inputRef={ref}
                  variant="standard"
                  error={!!error}
                  helperText={error?.message}
                />
              )}
            />
          );
        }}
      />
    </div>
  );
};

export default SelectCountryField;
