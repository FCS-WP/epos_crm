import React, { useState } from "react";
import AuthDialog from "./auth/AuthDialog";

const LoginForm = ({ isOpen,tenant }) => {
  const [open, setOpen] = useState(isOpen);
  return (
    <>
      <AuthDialog tenant={tenant} open={open} onClose={() => setOpen(false)} />
    </>
  );
};

export default LoginForm;
