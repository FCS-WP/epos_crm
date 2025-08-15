import React, { useState, useEffect } from "react";
import { Menu, MenuItem, Button } from "@mui/material";
const LoginDropdown = () => {
  const [anchorEl, setAnchorEl] = useState(null);
  const open = Boolean(anchorEl);

  const handleMouseEnter = (event) => {
    setAnchorEl(event.currentTarget);
  };

  const handleMouseLeave = () => {
    setAnchorEl(null);
  };

  return (
    <div onMouseLeave={handleMouseLeave}>
      {/* <Button
        id="menu-trigger"
        aria-controls={open ? "hover-menu" : undefined}
        aria-haspopup="true"
        aria-expanded={open ? "true" : undefined}
        onMouseEnter={handleMouseEnter}
        variant="contained"
      ></Button> */}

      <Menu
        id="hover-menu"
        anchorEl={anchorEl}
        open={open}
        onClose={handleMouseLeave}
        MenuListProps={{
          onMouseLeave: handleMouseLeave,
          "aria-labelledby": "menu-trigger",
        }}
      >
        <MenuItem onClick={handleMouseLeave}>Profile</MenuItem>
        <MenuItem onClick={handleMouseLeave}>My account</MenuItem>
        <MenuItem onClick={handleMouseLeave}>Logout</MenuItem>
      </Menu>
    </div>
  );
};

export default LoginDropdown;
