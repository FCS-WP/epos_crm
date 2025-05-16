import React, { useState, useEffect } from "react";
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
} from "@mui/material";
import { Api } from "../api";

const Index = () => {
  const [eposUrl, setEposUrl] = useState("");
  const [eposTenant, setEposTenant] = useState("");
  const [termsAccepted, setTermsAccepted] = useState(false);

  const [errors, setErrors] = useState({});
  const [loading, setLoading] = useState(false);
  const [connectEPOS, setConnectEPOS] = useState(false);

  // Helper: Validate URL format
  const isValidUrl = (string) => {
    try {
      new URL(string);
      return true;
    } catch (_) {
      return false;
    }
  };

  // Helper: Extract subdomain from URL
  const getSubDomain = (url) => {
    try {
      const hostname = new URL(url).hostname;
      const parts = hostname.split(".");
      return parts.length > 2 ? parts[0] : null;
    } catch (e) {
      return null;
    }
  };

  // Form submit handler
  const handleSubmit = async (e) => {
    e.preventDefault();
    const newErrors = {};

    if (!eposUrl.trim()) {
      newErrors.eposUrl = "Authentication with EPOS Backend is required";
    } else if (!isValidUrl(eposUrl)) {
      newErrors.eposUrl = "Please enter a valid URL";
    }

    if (!termsAccepted) {
      newErrors.terms =
        "Please tick the checkbox (consent PDPA) above to continue";
    }

    setErrors(newErrors);

    if (Object.keys(newErrors).length === 0) {
      setLoading(true);
      setConnectEPOS(true);
      await updateConfig();
    }
  };

  // Build and redirect to EPOS connect URL
  const connect = (params) => {
    const baseUrl = process.env.EPOS_CONNECT_URL;
    const queryString = new URLSearchParams(params).toString();
    const fullUrl = `${baseUrl}?${queryString}`;

    console.log("Redirecting to:", fullUrl);
    window.location.href = fullUrl;
  };

  // Fetch existing values
  const fetchData = async () => {
    try {
      setLoading(true);
      const keys = {
        option_name: ["epos_be_url", "consent_pdpa"],
      };
      const { data } = await Api.checkKeyExits(keys);
      setEposUrl(data.epos_be_url || "");
      setTermsAccepted(!!data.consent_pdpa);
      setEposTenant(getSubDomain(data.epos_be_url));
    } catch (err) {
      console.error("Failed to fetch EPOS settings", err);
    } finally {
      setLoading(false);
    }
  };

  // Save new values and redirect
  const updateConfig = async () => {
    try {
      const keys = {
        option_name: ["epos_be_url", "consent_pdpa"],
        option_data: [eposUrl, termsAccepted],
      };
      const { data } = await Api.updateKeys(keys);

      setEposUrl(data.epos_be_url || "");
      setEposTenant(getSubDomain(data.epos_be_url));
      setTermsAccepted(!!data.consent_pdpa);

      const params = {
        client_id: process.env.EPOS_API_KEY,
        redirect_uri: `${getDomainName()}/wp-admin/admin.php?page=wc-settings&tab=epos_crm`,
        subdomain: getSubDomain(data.epos_be_url),
      };

      connect(params);
    } catch (err) {
      console.error("Failed to update EPOS settings", err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchData();
  }, []);

  return (
    <div>
      <Table style={{ width: "700px" }} className="form-table">
        <TableBody>
          <TableRow>
            <TableCell component="th" scope="row" className="titledesc">
              <label htmlFor="epos_be_url">
                Authentication with EPOS backend
                <span style={{ color: "#cc0000" }}>*</span>
              </label>
            </TableCell>
            <TableCell className="forminp forminp-text">
              <Input
                id="epos_be_url"
                name="epos_be_url"
                value={eposUrl}
                onChange={(e) => {
                  setEposUrl(e.target.value);
                  if (errors.eposUrl) {
                    setErrors((prev) => ({ ...prev, eposUrl: undefined }));
                  }
                }}
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
                <strong>
                  <span style={{ color: "#cc0000" }}>*</span> EPOS V5 Backend
                </strong>{" "}
                may collect, use, and disclose your personal data, which you
                have provided in this form for providing marketing material that
                you have agreed to receive, in accordance with the Personal Data
                Protection Act 2012 and our protection policy.
              </p>
            </TableCell>
          </TableRow>

          <TableRow>
            <TableCell colSpan={2}>
              <FormControlLabel
                control={
                  <Checkbox
                    id="consent_pdpa"
                    name="consent_pdpa"
                    checked={termsAccepted}
                    onChange={(e) => {
                      setTermsAccepted(e.target.checked);
                      if (errors.terms) {
                        setErrors((prev) => ({ ...prev, terms: undefined }));
                      }
                    }}
                    color="primary"
                  />
                }
                label="I have read and agree with the terms and conditions."
                sx={{ span: { fontSize: "14px" } }}
              />

              <FormHelperText error>{errors.terms}</FormHelperText>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>

      <p className="buttonSubmit">
        <button
          type="submit"
          className="woocommerce-save-button components-button is-primary"
          onClick={handleSubmit}
        >
          {connectEPOS ? (
            <CircularProgress size={20} color="inherit" />
          ) : (
            "Connect"
          )}
        </button>
      </p>
    </div>
  );
};

export default Index;
