import React from "react";
import theme from "../theme/customTheme";
import { CssBaseline, ThemeProvider } from "@mui/material";
import { ToastContainer } from "react-toastify";

document.addEventListener("DOMContentLoaded", function () {
  const zippyMain = document.getElementById("zippy-form");

  if (typeof zippyMain != "undefined" && zippyMain != null) {
    const root = ReactDOM.createRoot(zippyMain);
    root.render(      
    <ThemeProvider theme={theme}>
      <CssBaseline /> 
      <>Shin</>
      <ToastContainer />
    </ThemeProvider>);
  }
});
