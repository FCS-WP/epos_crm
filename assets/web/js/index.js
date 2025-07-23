import React from "react";
import ReactDOM from "react-dom/client";
import theme from "../theme/customTheme";
import { CssBaseline, ThemeProvider } from "@mui/material";
import { ToastContainer } from "react-toastify";
import LoginForm from "./Components/LoginForm";
import PointInformation from "./Components/PointInformation";

document.addEventListener("DOMContentLoaded", function () {
  const zippyMain = document.getElementById("epos_crm_login_form");
  const epos_login_icon = document.getElementById("epos_crm_login");
  const epos_crm_user_name = document.getElementById("epos_crm_user_name");
  const epos_crm_point_information = document.getElementById(
    "epos_crm_point_information"
  );

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

  if (epos_crm_point_information) {
    const pointInfoRoot = ReactDOM.createRoot(epos_crm_point_information);
    const points = parseInt(epos_crm_point_information?.dataset?.points) || 0;
    const pointRate =
      parseFloat(epos_crm_point_information?.dataset?.pointRate) || 0;
       const cartTotal =
      parseFloat(epos_crm_point_information?.dataset?.cartTotal) || 0;
    pointInfoRoot.render(
      <ThemeProvider theme={theme}>
        <CssBaseline />
        <PointInformation isOpen={true} points={points} pointRate={pointRate} cartTotal={cartTotal} />
        <ToastContainer />
      </ThemeProvider>
    );
  }
});
