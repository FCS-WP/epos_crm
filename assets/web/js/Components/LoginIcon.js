import React, { useEffect, useState } from "react";
import { CircularProgress } from "@mui/material";
const LoginIcon = ({ customerName, ...props }) => {
  return customerName ? (
    <span>{customerName}</span>
  ) : (
    <CircularProgress size="30px" />
  );
};

export default LoginIcon;
