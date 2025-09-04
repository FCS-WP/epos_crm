import React, { useState } from "react";
import AuthDialog from "./auth/AuthDialog";

const LoginForm = ({ tenant, isOpen }) => {
  const [open, setOpen] = useState(isOpen);

  const show = () => setOpen(true);
  const hide = () => setOpen(false);

  if (typeof window !== "undefined") {
    window.EposLoginForm = { show, hide };
  }

  return (
    <AuthDialog
      tenant={tenant}
      open={open}
      onClose={hide} 
    />
  );
};

export default LoginForm;
