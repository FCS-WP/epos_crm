import React from "react";

const ToastContent = ({
  method,
  title,
  subtitle,
  showAction = false,
  actionLabel = "Action",
  HandleAction = () => {},
}) => {
  return (
    <div style={{ display: "flex", flexDirection: "column" }}>
      <strong style={{ fontSize: "1rem", textTransform: "capitalize" }}>
        {method}
      </strong>
      {subtitle && (
        <div style={{ fontSize: "12px", marginTop: "2px" }}>{subtitle}</div>
      )}
      {showAction && (
        <button
          onClick={HandleAction}
          style={{
            marginTop: "8px",
            alignSelf: "flex-start",
            background: "transparent",
            color: "#007bff",
            border: "none",
            padding: 0,
            cursor: "pointer",
            fontSize: "0.875rem",
            textDecoration: "underline",
          }}
        >
          {actionLabel}
        </button>
      )}
    </div>
  );
};

export default ToastContent;
