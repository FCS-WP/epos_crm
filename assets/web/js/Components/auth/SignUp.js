// [Unchanged imports]
import React, { useState, useEffect } from "react";
import {
  Button,
  Checkbox,
  Typography,
  FormControlLabel,
  Grid,
  Box,
  FormHelperText,
} from "@mui/material";
import { useForm, Controller } from "react-hook-form";
import { yupResolver } from "@hookform/resolvers/yup";
import * as yup from "yup";
import parsePhoneNumberFromString from "libphonenumber-js";
import { webApi } from "../../api";
import PasswordField from "../common/FormFields/PasswordField";
import Toast from "../common/notifications/toast";
import PhoneField from "../common/FormFields/PhoneField";
import InputField from "../common/FormFields/InputField";
import SelectCountryField from "../common/FormFields/SelectCountryField";
import countryList from "../../Helper/countries";
import { emailRegex } from "../../Helper/email-regex";

const schema = yup.object().shape({
  full_name: yup.string().required("Full name is a required field"),
  phone_number: yup
    .string()
    .transform((value) => {
      if (typeof value === "string") {
        const cleaned = value
          .trim()
          .replace(/^(\+65|65)/, "") // remove +65 or 65 prefix
          .replace(/[\s-]/g, "");

        if (/^[689]\d{7}$/.test(cleaned)) {
          return `+65 ${cleaned}`;
        }

        return value.trim();
      }
      return value;
    })
    .test("sg-phone", "Invalid Phone number", (value) => {
      if (typeof value === "string" && value.startsWith("+65")) {
        return /^\+65\s[689]\d{7}$/.test(value);
      }
      return true;
    })
    .required("Phone Number is a required field"),

  email: yup
    .string()
    .email("Invalid email")
    .matches(emailRegex, "Invalid email")
    .required("Email is a required field"),

  address_street_1: yup
    .string()
    .transform((value) => (typeof value === "string" ? value.trim() : value))
    .required("Address is a required field"),

  address_street_2: yup.string(),
  address_country: yup.string().required("Country is a required field"),
  address_postal_code: yup
    .number("Invalid postal code")
    .typeError("Invalid postal code")
    .required("Postal code is a required field"),

  address_city: yup.string(),
  password: yup
    .string()
    .min(6, "Password must be at least 6 characters")
    .required("Password is a required field"),
  confirmPassword: yup
    .string()
    .oneOf([yup.ref("password")], "Passwords doesn't match")
    .required("Confirm password is a required field"),
  accepted: yup
    .bool()
    .oneOf([true], "Please tick the checkbox(consent PDPA) above to continue"),
});

const SignUp = ({ setTab, ...props }) => {
  const {
    control,
    handleSubmit,
    formState: { errors },
  } = useForm({ resolver: yupResolver(schema), method: "onChange" });

  const [loading, setLoading] = useState(false);
  const [siteName, setSiteName] = useState("");
  const buildPhoneParam = (phone_number) => {
    try {
      const parsed = parsePhoneNumberFromString(phone_number);
      return {
        phone_code: `${parsed.countryCallingCode}`,
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
    const { phone_code, national_number } = buildPhoneParam(data.phone_number);
    const registerData = {
      ...data,
      phone_code,
      address_street_1: data.address_street_1.trim(),
      phone_number: national_number,
      confirm_password: data.confirmPassword,
    };

    try {
      const { data } = await webApi.registerAccount(registerData);
      if (data && data?.status == "success") {
        Toast({
          method: "success",
          subtitle: "Account created successfully.",
        });
        setTab(0);
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

  useEffect(() => {
    const element = document.getElementById("epos_crm_login_form");
    if (element) {
      const siteName = element.dataset.siteName;
      setSiteName(siteName);
    }
  }, []);

  return (
    <Box sx={{ maxWidth: 500, mx: "auto", mt: 4 }}>
      <Typography className="get-started" variant="h4" gutterBottom>
        Register Now!
      </Typography>

      <form onSubmit={handleSubmit(onSubmit)} noValidate>
        <Grid container spacing={2}>
          <Grid size={12}>
            <InputField
              label="Full Name"
              name="full_name"
              control={control}
              error={errors.full_name}
              required={true}
            />
          </Grid>

          <Grid size={12}>
            <PhoneField
              label="Phone Number"
              name="phone_number"
              control={control}
              error={errors.phone_number}
            />
          </Grid>

          <Grid size={12}>
            <InputField
              label="Email Address"
              name="email"
              control={control}
              error={errors.email}
              required={true}
            />
          </Grid>

          <Grid size={12}>
            <InputField
              label="Address 1"
              name="address_street_1"
              control={control}
              error={errors.address_street_1}
              required={true}
            />
          </Grid>

          <Grid size={12}>
            <InputField
              label="Address 2"
              name="address_street_2"
              control={control}
              error={errors.address_street_2}
            />
          </Grid>

          <Grid size={12}>
            <SelectCountryField
              label="Country"
              name="address_country"
              defaultValue="SG"
              control={control}
              error={errors.address_country}
              data={countryList}
            />
          </Grid>

          <Grid size={12}>
            <InputField
              label="Postal code"
              name="address_postal_code"
              control={control}
              error={errors.address_postal_code}
              required={true}
            />
          </Grid>

          <Grid size={12}>
            <InputField
              label="City"
              name="address_city"
              control={control}
              error={errors.address_city}
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

          <Grid size={12}>
            <PasswordField
              label="Confirm Password"
              name="confirmPassword"
              control={control}
              error={errors.confirmPassword}
            />
          </Grid>

          <Grid size={12}>
            <Typography className="epos-term" variant="body2" fontSize="12px">
              {siteName && <strong className="admin-name">{siteName} </strong>}
              may collect, use and disclose your personal data, which you have
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
              error={errors.accepted}
              render={({ field }) => (
                <>
                  <FormControlLabel
                    control={<Checkbox {...field} checked={field.value} />}
                    label={
                      <Typography
                        className="epos-term checkbox"
                        variant="body2"
                      >
                        I have read and agree with the Terms and Conditions
                      </Typography>
                    }
                  />
                  {errors.accepted && (
                    <FormHelperText error>
                      {errors.accepted.message}
                    </FormHelperText>
                  )}
                </>
              )}
            />
          </Grid>

          <Grid size={4}>
            <Button
              type="submit"
              className="epos-btn"
              size="small"
              loading={loading}
            >
              Submit
            </Button>
          </Grid>
        </Grid>
      </form>
    </Box>
  );
};

export default SignUp;
