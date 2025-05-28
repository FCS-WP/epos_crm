// [Unchanged imports]
import React, { useState } from "react";
import {
  TextField,
  Button,
  Checkbox,
  Typography,
  OutlinedInput,
  FormControl,
  IconButton,
  InputAdornment,
  FormControlLabel,
  Grid,
  Box,
} from "@mui/material";
import Visibility from "@mui/icons-material/Visibility";
import VisibilityOff from "@mui/icons-material/VisibilityOff";
import { useForm, Controller } from "react-hook-form";
import { yupResolver } from "@hookform/resolvers/yup";
import * as yup from "yup";
import { toast } from "react-toastify";
import { webApi } from "../../api";

const schema = yup.object().shape({
  fullName: yup.string().required("Full name is required"),
  phone: yup.string().required("Phone number is required"),
  email: yup.string().email("Invalid email").required("Email is required"),
  address: yup.string().required("Address is required"),
  password: yup
    .string()
    .min(6, "Password must be at least 6 characters")
    .required(),
  confirmPassword: yup
    .string()
    .oneOf([yup.ref("password")], "Passwords must match")
    .required("Confirm password is required"),
  accepted: yup.bool().oneOf([true], "You must accept the terms"),
});

// const siteName = document.getElementById('epos_crm_login_form').
const SignUp = () => {
  const {
    control,
    handleSubmit,
    formState: { errors },
  } = useForm({ resolver: yupResolver(schema) });

  const [showPassword, setShowPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);
  const [loading, setLoading] = useState(false);

  const togglePasswordVisibility = () => setShowPassword((prev) => !prev);
  const toggleConfirmPasswordVisibility = () =>
    setShowConfirmPassword((prev) => !prev);

  const onSubmit = async (data) => {
    setLoading(true);
    const { fullName, phone, email, password, confirmPassword } = data;
    const registerData = {
      first_name: fullName,
      last_name: phone,
      email,
      password,
      confirm_password: confirmPassword,
    };

    try {
      const response = await webApi.registerAccount(registerData);
      if (!response || response?.data.status !== "success") {
        throw new Error(
          response?.data.message ?? "Could not register. Try again later."
        );
      }
      toast.success("Account created successfully.");
      // setTab(0); // Assuming this is navigation logic
    } catch (err) {
      toast.error(err.message);
    } finally {
      setLoading(false);
    }
  };

  return (
    <Box sx={{ maxWidth: 500, mx: "auto", mt: 4 }}>
      <Typography className="get-started" variant="h4" gutterBottom>
        Register Now!
      </Typography>

      <form onSubmit={handleSubmit(onSubmit)} noValidate>
        <Grid container spacing={2}>
          <Grid item size={12}>
            <Typography className="input-label">Full Name</Typography>
            <Controller
              name="fullName"
              control={control}
              defaultValue=""
              render={({ field }) => (
                <TextField
                  {...field}
                  fullWidth
                  size="small"
                  error={!!errors.fullName}
                  helperText={errors.fullName?.message}
                />
              )}
            />
          </Grid>

          <Grid item size={12}>
            <Typography className="input-label">Phone Number</Typography>
            <Controller
              name="phone"
              control={control}
              defaultValue=""
              render={({ field }) => (
                <TextField
                  {...field}
                  fullWidth
                  size="small"
                  error={!!errors.phone}
                  helperText={errors.phone?.message}
                />
              )}
            />
          </Grid>

          <Grid item size={12}>
            <Typography className="input-label">Email Address</Typography>
            <Controller
              name="email"
              control={control}
              defaultValue=""
              render={({ field }) => (
                <TextField
                  {...field}
                  type="email"
                  fullWidth
                  size="small"
                  error={!!errors.email}
                  helperText={errors.email?.message}
                />
              )}
            />
          </Grid>

          <Grid item size={12}>
            <Typography className="input-label">Address</Typography>
            <Controller
              name="address"
              control={control}
              defaultValue=""
              render={({ field }) => (
                <TextField
                  {...field}
                  fullWidth
                  size="small"
                  error={!!errors.address}
                  helperText={errors.address?.message}
                />
              )}
            />
          </Grid>

          <Grid item size={12}>
            <Typography className="input-label">Password</Typography>
            <Controller
              name="password"
              control={control}
              defaultValue=""
              render={({ field }) => (
                <FormControl fullWidth size="small" variant="outlined">
                  <OutlinedInput
                    {...field}
                    type={showPassword ? "text" : "password"}
                    placeholder="Enter password"
                    endAdornment={
                      <InputAdornment position="end">
                        <IconButton
                          onClick={togglePasswordVisibility}
                          edge="end"
                          className="hide-password"
                        >
                          {showPassword ? <VisibilityOff /> : <Visibility />}
                        </IconButton>
                      </InputAdornment>
                    }
                    error={!!errors.password}
                  />
                  {errors.password && (
                    <Typography variant="caption" color="error">
                      {errors.password.message}
                    </Typography>
                  )}
                </FormControl>
              )}
            />
          </Grid>

          <Grid item size={12}>
            <Typography className="input-label">Confirm Password</Typography>
            <Controller
              name="confirmPassword"
              control={control}
              defaultValue=""
              render={({ field }) => (
                <FormControl fullWidth size="small" variant="outlined">
                  <OutlinedInput
                    {...field}
                    type={showConfirmPassword ? "text" : "password"}
                    placeholder="Confirm password"
                    endAdornment={
                      <InputAdornment position="end">
                        <IconButton
                          onClick={toggleConfirmPasswordVisibility}
                          edge="end"
                          className="hide-password"
                        >
                          {showConfirmPassword ? (
                            <VisibilityOff />
                          ) : (
                            <Visibility />
                          )}
                        </IconButton>
                      </InputAdornment>
                    }
                    error={!!errors.confirmPassword}
                  />
                  {errors.confirmPassword && (
                    <Typography variant="caption" color="error">
                      {errors.confirmPassword.message}
                    </Typography>
                  )}
                </FormControl>
              )}
            />
          </Grid>

          <Grid item size={12}>
            <Typography className="epos-term" variant="body2" fontSize="12px">
              <strong className="admin-name">This is strong text</strong> may
              collect, use and disclose your personal data, which you have
              provided in this form, for providing marketing material that you
              have agreed to receive, in accordance with the Personal Data
              Protection Act 2012 and our data protection policy.
            </Typography>
          </Grid>

          <Grid size={8}>
            <Controller
              name="accepted"
              control={control}
              defaultValue={false}
              render={({ field }) => (
                <FormControlLabel
                  control={<Checkbox {...field} checked={field.value} />}
                  label={
                    <Typography className="epos-term checkbox" variant="body2">
                      I have read and agree with the Terms and Conditions
                    </Typography>
                  }
                />
              )}
            />
          </Grid>

          <Grid item size={4}>
            <Button
              type="submit"
              className="epos-btn"
              size="small"
              disabled={loading}
            >
              {loading ? "Submitting..." : "Submit"}
            </Button>
          </Grid>
        </Grid>
      </form>
    </Box>
  );
};

export default SignUp;
