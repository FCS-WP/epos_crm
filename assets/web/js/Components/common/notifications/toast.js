import React from "react";
import { toast } from "react-toastify";
import ToastContent from "./ToastContent";

const Toast = (args) => {
  const { method, options, ...rest } = args;

  toast[method](<ToastContent method={method} {...rest} />, options);
};

export default Toast;
