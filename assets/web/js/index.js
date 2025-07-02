import React from "react";
import ReactDOM from "react-dom/client";
import theme from "../theme/customTheme";
import { CssBaseline, ThemeProvider } from "@mui/material";
import { ToastContainer } from "react-toastify";
import LoginForm from "./Components/LoginForm";

document.addEventListener("DOMContentLoaded", function () {
  const zippyMain = document.getElementById("epos_crm_login_form");
  const checkout = zippyMain.dataset.checkout === "true";
  const epos_login_icon = document.getElementById("epos_crm_login");
  const isLogin = zippyMain.dataset.login === "true";
  const root = ReactDOM.createRoot(zippyMain);

  if (checkout && typeof zippyMain != "undefined" && zippyMain != null) {
    root.render(
      <ThemeProvider theme={theme}>
        <CssBaseline />
        <LoginForm isOpen={!isLogin} />
        <ToastContainer />
      </ThemeProvider>
    );
  }

  if (typeof epos_login_icon != "undefined" && epos_login_icon != null) {
    epos_login_icon.addEventListener("click", function (e) {
      root.render(
        <ThemeProvider theme={theme}>
          <CssBaseline />
          <LoginForm isOpen={true} />
          <ToastContainer />
        </ThemeProvider>
      );
    });
  }
});
