import React, { useState, useEffect, useRef } from "react";
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
  CircularProgress,
  Chip,
} from "@mui/material";
import { Api, eposApi } from "../api";

const AuthenticationForm = (eposUrl, termsAccepted, ...props) => {
  const [errors, setErrors] = useState({});
  const [loading, setLoading] = useState(false);
  const checkbox = useRef();
  const handleSubmit = async (e) => {
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

    if (Object.keys(newErrors).length === 0) {
      setLoading(true);
      updateConfig();
    }
  };

  const connect = (params) => {
    const baseUrl = process.env.EPOS_CONNECT_URL;
    const queryString = new URLSearchParams(params).toString();
    const fullUrl = `${baseUrl}?${queryString}`;

    console.log("Redirecting to:", fullUrl);
    window.location.href = fullUrl;
  };

  const handleInputChange = (e) => {
    setEposUrl(e.target.value);
  };

  const updateConfig = async () => {
    try {
      setLoading(true);

      const keys = {
        option_name: ["epos_be_url", "consent_pdpa"],
        option_data: [eposUrl, termsAccepted],
      };
      const { data } = await Api.updateKeys(keys);
    } catch (err) {
      console.error("Failded update EPOS settings", err);
    } finally {
      setLoading(false);
      const params = {
        client_id: process.env.EPOS_CLIENT_KEY,
        redirect_uri: `${getDomainName()}/wp-admin/admin.php?page=wc-settings&tab=epos_crm`,
        subdomain: "shin",
      };
      connect(params);
    }
  };

  useEffect(() => {
    // fetchData();
  }, []);

  return (
    <form onSubmit={handleSubmit}>
      <Table style={{ width: "700px" }} className="form-table">
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
                onChange={handleInputChange}
                fullWidth
                error={!!errors.eposUrl}
                disabled={loading}
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
                your personal data, which you have provided in this form for
                providing marketing material that you have agreed to receive, in
                accordance with the Personal Data Protection Act 2012 and our
                protection policy.
              </p>
            </TableCell>
          </TableRow>

          <TableRow>
            <TableCell colSpan={2}>
              <FormControlLabel
                control={
                  <Checkbox
                    ref={checkbox}
                    id="consent_pdpa"
                    name="consent_pdpa"
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
          disabled={loading}
        >
          {loading ? <CircularProgress size={20} color="inherit" /> : "Connect"}
        </button>
      </p>

      <div className="epos-crm-wrapper">
        <div className="status-details">
          <span>
            <strong>EPOS Backend: </strong>
          </span>
          <span>https://pm.floatingcube.com/T15187</span>
        </div>
        <div className="status-details">
          <span>
            <strong>Status: </strong>
          </span>
          <span className="epos-crm-sucess">Connected</span>
        </div>
      </div>
    </form>
  );
};

export default AuthenticationForm;
