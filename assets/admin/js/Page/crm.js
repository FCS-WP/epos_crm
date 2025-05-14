import React, { useState, useCallback } from "react";
import { getDomainName } from "../utils/helper";
import {
  Input,
  Table,
  TableBody,
  TableRow,
  TableCell,
  Checkbox,
  FormControlLabel,
  FormHelperText,
} from "@mui/material";

import { eposApi } from "../api";
const Index = () => {
  const [eposUrl, setEposUrl] = useState("");
  const [termsAccepted, setTermsAccepted] = useState(false);
  const [errors, setErrors] = useState({});
  const [loadingState, setLoadingState] = useState(false);
  const handleSubmit = (e) => {
    e.preventDefault();

    const newErrors = {};
    if (!eposUrl.trim()) {
      newErrors.eposUrl = "Authentication with EPOS Backend is required";
    }
    if (!termsAccepted) {
      newErrors.terms =
        "Please tick the checkbox (consent PDPA) above to continue";
    }

    setErrors(newErrors);

    // If no errors, proceed with your logic (e.g., API call)
    if (Object.keys(newErrors).length === 0) {
      const params = {
        client_id: process.env.EPOS_CLIENT_KEY,
        redirect_uri:
          getDomainName() + "/wp-admin/admin.php?page=wc-settings&tab=epos_crm",
        subdomain: "shin",
      };
      connect(params);
    }
  };

  const connect = (params) => {
    const baseUrl = "https://livedevs.com/connect";

    const queryString = new URLSearchParams(params).toString();

    console.log(`${baseUrl}?${queryString}`);

    // window.location.href = `${baseUrl}?${queryString}`;
  };

  // const connect = useCallback(async (params) => {
  //   try {
  //     setLoadingState(true);

  //     const { data: responseData } = await eposApi.connect(params);
  //   } catch (error) {
  //     console.error("Error fetching bookings data:", error);
  //   } finally {
  //     setLoadingState((prev) => ({ ...prev, global: false }));
  //   }
  // }, []);
  const handleInputChange = (e) => {
    setEposUrl(e.target.value);
  };

  console.log();
  return (
    <form onSubmit={handleSubmit}>
      <Table className="form-table">
        <TableBody>
          <TableRow>
            <TableCell component="th" scope="row" className="titledesc">
              <label htmlFor="epos_be_url">
                Authentication with EPOS backend
              </label>
            </TableCell>
            <TableCell className="forminp forminp-text">
              <Input
                id="epos_be_url"
                name="epos_be_url"
                value={eposUrl}
                onChange={(e) => handleInputChange(e)}
                fullWidth
                error={Boolean(errors.eposUrl)}
              />
              {errors.eposUrl ? (
                <FormHelperText error>{errors.eposUrl}</FormHelperText>
              ) : (
                <p className="description">Your EPOS Backend URL</p>
              )}
            </TableCell>
          </TableRow>

          <TableRow>
            <TableCell colSpan={2}>
              <p>
                <span style={{ color: "#cc0000" }}>*</span>
                <strong> EPOS V5 Backend</strong> may collect, use, and disclose
                your personal data, which you have provided in this form <br />
                for providing marketing material that you have agreed to
                receive, in accordance with the Personal Data
                <br />
                Protection Act 2012 and our protection policy.
              </p>
            </TableCell>
          </TableRow>

          <TableRow>
            <TableCell colSpan={2}>
              <FormControlLabel
                control={
                  <Checkbox
                    checked={termsAccepted}
                    onChange={(e) => setTermsAccepted(e.target.checked)}
                    color="primary"
                  />
                }
                label="I have read and agree with the terms and conditions."
                sx={{ span: { fontSize: "14px" } }}
              />
              {errors.terms && (
                <FormHelperText error>{errors.terms}</FormHelperText>
              )}
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>

      <p className="buttonSubmit">
        <button
          type="submit"
          className="woocommerce-save-button components-button is-primary"
        >
          Connect
        </button>
      </p>
    </form>
  );
};

export default Index;
