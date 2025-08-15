import React, { useState, useEffect } from "react";
import eposLogo from "../../../icons/eposLogo.png";
import {
  Dialog,
  DialogContent,
  Tabs,
  Tab,
  Typography,
  Box,
} from "@mui/material";

import SignIn from "./SignIn";
import SignUp from "./SignUp";

const AuthDialog = ({ open, onClose, tenant }) => {
  const [tab, setTab] = useState(0); // 0: Sign In, 1: Sign Up
  const [hideTab, setHideTab] = useState(false);
  const [siteLogo, setSiteLogo] = useState("");

  useEffect(() => {
    const element = document.getElementById("epos_crm_login_form");
    if (element?.dataset?.siteLogo) {
      setSiteLogo(element.dataset.siteLogo);
    }
  }, [hideTab]);

  const handleTabChange = (event, newValue) => {
    setTab(newValue);
  };

  const handleHideTabTitle = (hide) => {
    setHideTab(hide);
  };

  const handleOnClose = (event, reason) => {
    if (reason !== "backdropClick") {
      onClose(event);
    }
  };

  const renderTabPrompt = () => {
    return (
      <Box mt={3} textAlign="center" className="epos-tab-form">
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
  };

  return (
    <Dialog
      open={open}
      onClose={handleOnClose}
      disableEscapeKeyDown
      maxWidth="xs"
      className="epos-crm-form"
      sx={{ padding: 0, margin: 0 }}
    >
      <DialogContent sx={{ padding: "20px 12px" }}>
        <Box textAlign="center">
          <img
            style={{ width: "150px", marginBottom: "20px" }}
            src={siteLogo || eposLogo}
            alt="EPOS Site Logo"
          />
        </Box>

        {tab === 0 ? (
          <SignIn
            handleMissingEmail={handleHideTabTitle}
            handleClosePopup={onClose}
            tenant={tenant}
          />
        ) : (
          <SignUp setTab={setTab} />
        )}

        {!hideTab && renderTabPrompt()}
      </DialogContent>
    </Dialog>
  );
};

export default AuthDialog;
