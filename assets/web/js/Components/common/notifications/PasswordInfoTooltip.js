import React from "react";
import { Typography, Link, Box } from "@mui/material";

const PasswordInfoTooltip = ({ tenant }) => {
  return (
    <Box display={"flex"} flexDirection={"column"} sx={{ maxWidth: 250 }}>
      <Typography variant="body2" fontWeight="bold" gutterBottom>
        Existing users log in for the first time?
      </Typography>
      <Typography variant="body2" gutterBottom>
        Your default password is your registered phone number.
      </Typography>
      <Typography variant="body2">
        To change your password, please log in to the{" "}
        <Link
          href={tenant}
          underline="hover"
          color="primary"
          target="_blank"
          rel="noopener"
        >
          Members' Portal
        </Link>{" "}
        and update it there.
      </Typography>
    </Box>
  );
};

export default PasswordInfoTooltip;
