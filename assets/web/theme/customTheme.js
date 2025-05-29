import { createTheme } from "@mui/material/styles";

const theme = createTheme({
  palette: {
    primary: {
      main: "#2C5CCB", // Use 'main' key
    },
    secondary: {
      main: "#DB3F2E",
    },
    white: {
      main: "#FFF",
    },
    text: {
      primary: "#000",
      secondary: "#666",
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
    MuiOutlinedInput: {
      styleOverrides: {
        notchedOutline: {
          border: 'none',
        },
        root: {
          '&:hover .MuiOutlinedInput-notchedOutline': {
            border: 'none',
          },
          '&.Mui-focused .MuiOutlinedInput-notchedOutline': {
            border: 'none',
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
          borderRadius: 8,
          border: "0px",
          backgroundColor: "#E8F0FE",
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
