import React from "react";
import ReactDOM from "react-dom/client";
import theme from "../theme/customTheme";
import { CssBaseline, ThemeProvider } from "@mui/material";
import { ToastContainer } from "react-toastify";
import LoginForm from "./Components/LoginForm";

document.addEventListener("DOMContentLoaded", function () {
  const zippyMain = document.getElementById("epos_crm_login_form");
  if (typeof zippyMain != "undefined" && zippyMain != null) {
    const root = ReactDOM.createRoot(zippyMain);
    root.render(
      <ThemeProvider theme={theme}>
        <CssBaseline />
        <LoginForm />
        <ToastContainer />
      </ThemeProvider>
    );
  }
});
