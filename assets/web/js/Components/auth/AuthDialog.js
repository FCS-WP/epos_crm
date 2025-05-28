import React, { useState } from "react";
import eposLogo from "../../../icon/eposLogo.png";
import { Dialog, DialogContent, Tabs, Tab } from "@mui/material";

import { toast } from "react-toastify";
import { webApi } from "../../api";
import SignIn from "./SignIn";
import SignUp from "./SignUp";

const AuthDialog = ({ open, onClose }) => {
  const [tab, setTab] = useState(0);

  return (
    <Dialog
      open={open}
      onClose={(event, reason) => {
        if (reason !== "backdropClick") {
          onClose(event);
        }
      }}
      maxWidth="xs"
      sx={{
        padding: "0",
      }}
      className="epos-crm-form"
    >
      <DialogContent>
        <img src={eposLogo} />
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
        {tab === 1 ? (
          <>
            <SignUp />
          </>
        ) : (
          <SignIn />
        )}
      </DialogContent>
    </Dialog>
  );
};

export default AuthDialog;
