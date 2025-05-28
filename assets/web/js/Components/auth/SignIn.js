import React, { useState } from "react";
import * as yup from "yup";
import { yupResolver } from "@hookform/resolvers/yup";
import { useForm } from "react-hook-form";
import parsePhoneNumberFromString from "libphonenumber-js";
import { webApi } from "../../api";

import { Button, Box, Typography, Grid } from "@mui/material";
// import InputField from "../common/FormFields/InputField";
import PasswordField from "../common/FormFields/PasswordField";
import Toast from "../common/notifications/toast";
import PhoneField from "../common/FormFields/PhoneField";
const schema = yup.object().shape({
  phone_number: yup.string().required("Phone Number is a required field"),

  password: yup.string().required("Password is a required field"),
});

const SignIn = ({ handleClosePopup, ...props }) => {
  const [loading, setLoading] = useState(false);

  const handleClose = () => {
    handleClosePopup();
  };
  const handleBackToShop = async () => {
    window.location.href = window.location.origin;
  };

  const buildPhoneParam = (phone_number) => {
    try {
      const parsed = parsePhoneNumberFromString(phone_number);
      return {
        phone_code: `+${parsed.countryCallingCode}`,
        national_number: parsed.nationalNumber,
      };
    } catch (error) {
      return {
        phone_code: "",
        national_number: "",
      };
    }
  };

  const onSubmit = async (data) => {
    setLoading(true);
    const { phone_number, password } = data;

    const { phone_code, national_number } = buildPhoneParam(phone_number);

    const loginData = {
      phone_code,
      phone_number: national_number,
      password,
    };

    try {
      const { data } = await webApi.loginAccount(loginData);
      // console.log(data.status);
      if (!data || data?.status !== "success") {
        Toast({
          method: "error",
          subtitle: "Invalid Phone number or password",
        });
      }
      Toast({
        method: "success",
        subtitle: "Login successfully.",
      });
      handleClose();
      window.location.reload();
    } catch (err) {
      Toast({
        method: "error",
        subtitle: "Invalid Phone number or password",
      });
    } finally {
      setLoading(false);
    }
  };

  const {
    control,
    handleSubmit,
    formState: { errors },
  } = useForm({ resolver: yupResolver(schema) });

  return (
    <form onSubmit={handleSubmit(onSubmit)} noValidate>
      <Grid container spacing={2}>
        <Grid size={12}>
          <Typography
            variant="h4"
            className="get-started"
            marginBottom={6}
            marginTop={4}
          >
            Let’s Get Started
          </Typography>
          <PhoneField
            label="Phone Number"
            name="phone_number"
            control={control}
            error={errors.phone_number}
          />
        </Grid>
        <Grid size={12}>
          <PasswordField
            label="Password"
            name="password"
            control={control}
            error={errors.password}
          />
        </Grid>
      </Grid>
      <Box
        display={"flex"}
        alignItems={"center"}
        flexDirection={"column"}
        gap={1}
        marginTop={5}
        className=""
      >
        <Button
          className="epos-btn"
          variant="contained"
          type="submit"
          loading={loading}
        >
          Login
        </Button>
        <Button
          className="epos-btn"
          variant="outline"
          onClick={handleBackToShop}
        >
          Back to Shop
        </Button>
      </Box>
    </form>
  );
};

export default SignIn;
