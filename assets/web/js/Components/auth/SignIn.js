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
import UpdateEmail from "./UpdateEmail";
import { isValidEmail } from "../../Helper/email-regex";

const loginSchema = yup.object().shape({
  phone_number: yup.string().required("Phone Number is a required field"),

  password: yup.string().required("Password is a required field"),
});

const SignIn = ({ handleClosePopup, handleMissingEmail, ...props }) => {
  const [loading, setLoading] = useState(false);
  const [isMissingEmail, setIsMissingEmail] = useState(false);
  const [currentUser, setCurrentUser] = useState(null);

  const handleClose = () => {
    reset();
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

      if (data && data?.status == "success") {
        const email = data?.data?.attributes?.email ?? "";
        if (!isValidEmail(email)) {
          Toast({
            method: "warning",
            subtitle: "Please update your email address to continue",
          });
          setIsMissingEmail(true);
          setCurrentUser(data?.data.id);
          handleMissingEmail(true);
        } else {
          Toast({
            method: "success",
            subtitle: "Login successfully.",
          });
          handleClose();
          setTimeout(function () {
            window.location.reload(1);
          }, 1000);
        }
      } else {
        Toast({
          method: "error",
          subtitle: data?.errors,
        });
      }
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
    reset,
  } = useForm({ resolver: yupResolver(loginSchema), mode: "onChange" });

  if (isMissingEmail) {
    return (
      <UpdateEmail currentUser={currentUser} handleClosePopup={handleClose} />
    );
  }

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
