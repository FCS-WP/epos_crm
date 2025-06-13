import { createTheme } from "@mui/material/styles";

const theme = createTheme({
  palette: {
    primary: {
      main: "#2271b1",
      contrastText: "#ffffff",
      pending: "#feb600",
      approve: "#009588",
      complete: "#007c00",
      approved: "#90caf9",
      cancelled: "#ef9a9a",
    },
    secondary: {
      main: "#216ba5",
    },

    background: {
      default: "#f5f5f5",
      paper: "#ffffff",
    },
    text: {
      primary: "#333333",
      secondary: "#666666",
    },
  },
  typography: {
    fontFamily:
      '"-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif',

    h1: {
      fontSize: "2.5rem",
      fontWeight: 700,
    },
    h2: {
      fontSize: "2rem",
      fontWeight: 600,
    },
    body1: {
      fontSize: "1rem",
      lineHeight: 1.5,
    },
  },
  components: {
    MuiButton: {
      styleOverrides: {
        root: {
          borderRadius: 8,
          textTransform: "none",
        },
      },
    },
    MuiFormHelperText: {
      styleOverrides: {
        root: {
          "&.Mui-error": {
            color: "#CC0000",
            fontStyle: "italic",
          },
        },
      },
    },
    MuiPaper: {
      styleOverrides: {
        root: {
          padding: "16px",
          boxShadow: "1px 2px 12px rgba(184, 193, 211, .4)",
        },
      },
    },
    MuiInput: {
      styleOverrides: {
        underline: {
          "&:before": {
            borderBottom: "none",
          },
          "&:hover:not(.Mui-disabled, .Mui-error):before": {
            borderBottom: "none",
          },
          "&:after": {
            borderBottom: "none",
          },
        },
      },
    },
    MuiTableCell: {
      styleOverrides: {
        root: {
          borderBottom: "none",
        },
      },
    },
    MuiInputBase: {
      styleOverrides: {
        root: {
          borderRadius: 4,
          border: "0px",
          backgroundColor: "#ffffff",
        },
        input: {
          // Styles for the input field
          padding: "10px",
          color: "#333",
        },
      },
    },
    MuiTypography: {
      styleOverrides: {
        h1: {
          fontWeight: "bold",
          fontSize: "1.5rem",
        },
        h2: {
          fontWeight: "bold",
          fontSize: "1.2rem",
        },
      },
      defaultProps: {
        variantMapping: {
          subtitle1: "h2",
          subtitle2: "h2",
          body1: "span",
          body2: "span",
        },
      },
    },
  },
});

export default theme;
