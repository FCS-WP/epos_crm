import React, { useState, useEffect } from "react";
import eposLogo from "../../../icons/eposLogo.png";
import { Dialog, DialogContent, Typography, Box } from "@mui/material";

import SignIn from "./SignIn";
import SignUp from "./SignUp";

const AuthDialog = ({ open, onClose, tenant }) => {
  const [tab, setTab] = useState(0); // 0: Sign In, 1: Sign Up
  const [hideTab, setHideTab] = useState(false);
  const [siteLogo, setSiteLogo] = useState("");

  useEffect(() => {
    if (open) {
      setTab(0);
      setHideTab(false);
    }
  }, [open]);

  // Load site logo from DOM dataset
  useEffect(() => {
    const element = document.getElementById("epos_crm_login_form");
    if (element?.dataset?.siteLogo) {
      setSiteLogo(element.dataset.siteLogo);
    }
  }, []);

  const handleOnClose = (event, reason) => {
    if (reason === "backdropClick") return;
    onClose(event);
  };

  const renderTabPrompt = () => (
    <Box mt={6} textAlign="center" className="epos-tab-form">
      <Typography variant="body2" className="epos-tab-title">
        {tab === 0 ? "Don't have an account?" : "Have an account?"}
        <Box
          className="epos-tab-link"
          component="span"
          sx={{
            cursor: "pointer",
            color: "primary.main",
            fontWeight: "bold",
            textDecoration: "underline",
            ml: 0.5,
          }}
          onClick={() => setTab(tab === 0 ? 1 : 0)}
        >
          {tab === 0 ? "Sign Up Now!" : "Log In Now"}
        </Box>
      </Typography>
    </Box>
  );

  return (
    <Dialog
      open={open}
      onClose={handleOnClose}
      disableEscapeKeyDown={true}
      maxWidth="xs"
      fullWidth
      className="epos-crm-form"
      aria-labelledby="auth-dialog-title"
      aria-describedby="auth-dialog-description"
      sx={{ padding: 0, margin: 0 }}
    >
      <DialogContent sx={{ padding: "20px 12px" }}>
        <Box textAlign="center">
          <img
            style={{ width: "150px", maxWidth: "150px", marginBottom: "20px" }}
            src={siteLogo || eposLogo}
            alt="EPOS Site Logo"
            className="epos-crm_logo"
          />
        </Box>

        {tab === 0 ? (
          <SignIn
            handleMissingEmail={setHideTab}
            handleClosePopup={onClose}
            tenant={tenant}
          />
        ) : (
          <SignUp setTab={setTab} />
        )}

        {!hideTab && renderTabPrompt()}

        <Box mt={6} textAlign="center" className="epos-tab-form">
          <Typography variant="body2" className="epos-tab-title">
            <Box
              className="epos-close-popup"
              component="span"
              sx={{
                cursor: "pointer",
                textDecoration: "underline",
                ml: 0.5,
              }}
              onClick={() => onClose()}
            >
              No, Thanks!
            </Box>
          </Typography>
        </Box>
      </DialogContent>
    </Dialog>
  );
};

export default AuthDialog;
