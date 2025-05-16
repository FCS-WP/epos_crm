import React from "react";
import ReactDOM from "react-dom/client";
import { ThemeProvider } from "@mui/material/styles";
import CssBaseline from "@mui/material/CssBaseline";
import theme from "../theme/theme";
import Index from "./Pages/crm";

function initializeApp() {
  const eposCRM = document.getElementById("epos_crm_root");

  if (eposCRM) {
    const root = ReactDOM.createRoot(eposCRM);
    root.render(
      <ThemeProvider theme={theme}>
        <CssBaseline />
        <Index />
      </ThemeProvider>
    );
  }
}

document.addEventListener("DOMContentLoaded", initializeApp);
