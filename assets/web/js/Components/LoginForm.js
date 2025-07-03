import React, { useState } from "react";
import AuthDialog from "./auth/AuthDialog";

const LoginForm = ({ isOpen }) => {
  const [open, setOpen] = useState(isOpen);
  return (
    <>
      <AuthDialog open={open} onClose={() => setOpen(false)} />
    </>
  );
};

export default LoginForm;
