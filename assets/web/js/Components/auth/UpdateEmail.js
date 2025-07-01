import React, { useState } from "react";
import * as yup from "yup";
import { yupResolver } from "@hookform/resolvers/yup";
import { set, useForm } from "react-hook-form";
import { webApi } from "../../api";
import { Button, Box, Typography, Grid } from "@mui/material";
import InputField from "../common/FormFields/InputField";
import Toast from "../common/notifications/toast";

const schema = yup.object().shape({
  email: yup
    .string()
    .email("Invalid email")
    .matches(/^[^@]+@[^@]+\.[^@]+$/, "Invalid email")
    .required("Email is a required field"),
});
const UpdateEmail = ({ currentUser, handleClosePopup, ...props }) => {
  const [loading, setLoading] = useState(false);

  const {
    control,
    handleSubmit,
    formState: { errors },
    reset,
  } = useForm({ resolver: yupResolver(schema), mode: "onChange" });

  const onSubmit = async (data) => {
    if (loading) return;
    setLoading(true);

    const updateData = {
      id: currentUser,
      customer: {
        email: data.email.trim(),
      },
    };

    try {
      const { data } = await webApi.updateAccount(updateData);

      if (data && data?.status == "success") {
        Toast({
          method: "success",
          subtitle: "Login successfully.",
        });

        reset();
        handleClosePopup();
        window.location.reload();
      } else {
        const errorMessage = data?.errors || "Failed to update email";
        Toast({
          method: "error",
          subtitle: errorMessage,
        });
      }
    } catch (err) {
      Toast({
        method: "error",
        subtitle: "An error occurred while updating your email",
      });
    } finally {
      setLoading(false);
    }
  };

  return (
    <form onSubmit={handleSubmit(onSubmit)} noValidate>
      <Grid container spacing={2}>
        <Grid size={12}>
          <Typography
            variant="h4"
            className="get-started update-email"
            marginBottom={4}
          >
            Please enter your email address to proceed!
          </Typography>
          <InputField
            label="Email Address"
            name="email"
            required={true}
            control={control}
            error={errors.email}
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
          Proceed
        </Button>
      </Box>
    </form>
  );
};

export default UpdateEmail;
