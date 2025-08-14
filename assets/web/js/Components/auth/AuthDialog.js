import React, { useState, useEffect } from "react";
import eposLogo from "../../../icons/eposLogo.png";
import { Dialog, DialogContent, Tabs, Tab } from "@mui/material";

import SignIn from "./SignIn";
import SignUp from "./SignUp";

const AuthDialog = ({ open, onClose, tenant }) => {
  const [tab, setTab] = useState(0);
  const [hideTab, setHideTab] = useState(false);
  const [siteLogo, setSiteLogo] = useState("");

  const handleOnClose = () => {
    onClose();
  };

  const handleHideTabTitle = (hide) => {
    setHideTab(hide);
  };

  useEffect(() => {
    const element = document.getElementById("epos_crm_login_form");
    if (element) {
      const siteLogo = element.dataset.siteLogo;
      setSiteLogo(siteLogo);
    }
  }, [hideTab]);

  return (
    <Dialog
      open={open}
      onClose={(event, reason) => {
        if (reason !== "backdropClick") {
          onClose(event);
        }
      }}
      disableEscapeKeyDown
      maxWidth="xs"
      sx={{
        padding: "0",
        margin: "0",
      }}
      className="epos-crm-form"
    >
      <DialogContent
        sx={{
          padding: "20px 12px",
        }}
      >
        <img
          style={{ width: "150px", marginBottom: "20px" }}
          src={siteLogo ?? eposLogo}
          alt="EPOS Logo"
        />
        {!hideTab && (
          <Tabs
            sx={{ mb: 3 }}
            value={tab}
            onChange={(e, newValue) => setTab(newValue)}
            centered
            className="epos-tab-form"
          >
            <Tab
              sx={{
                width: "50%",
              }}
              label="Login"
            />
            <Tab
              sx={{
                width: "50%",
              }}
              label="Register"
            />
          </Tabs>
        )}
        {tab === 1 ? (
          <>
            <SignUp setTab={setTab} />
          </>
        ) : (
          <SignIn
            handleMissingEmail={handleHideTabTitle}
            handleClosePopup={handleOnClose}
            tenant={tenant}
          />
        )}
      </DialogContent>
    </Dialog>
  );
};

export default AuthDialog;
