import React from "react";
import ReactDOM from "react-dom/client";
import theme from "../theme/customTheme";
import { CssBaseline, ThemeProvider } from "@mui/material";
import { ToastContainer } from "react-toastify";
import LoginForm from "./Components/LoginForm";
import LoginIcon from "./Components/LoginIcon";

document.addEventListener("DOMContentLoaded", function () {
  const zippyMain = document.getElementById("epos_crm_login_form");
  const epos_login_icon = document.getElementById("epos_crm_login");
  const epos_crm_user_name = document.getElementById("epos_crm_user_name");

  const checkout = zippyMain?.dataset?.checkout === "true";
  const isLogin = zippyMain?.dataset?.login === "true";
  const username = epos_crm_user_name?.dataset?.customerName || "";

  const formRoot = ReactDOM.createRoot(zippyMain);

  // Mount main login form in checkout
  if (checkout && zippyMain) {
    formRoot.render(
      <ThemeProvider theme={theme}>
        <CssBaseline />
        <LoginForm isOpen={!isLogin} />
        <ToastContainer />
      </ThemeProvider>
    );
  }

  // Icon click should show modal if not logged in
  if (epos_login_icon && username === "") {
    epos_login_icon.addEventListener("click", function (e) {
      formRoot.render(
        <ThemeProvider theme={theme}>
          <CssBaseline />
          <LoginForm isOpen={true} />
          <ToastContainer />
        </ThemeProvider>
      );
    });
  }
});
