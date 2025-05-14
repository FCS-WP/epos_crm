// utils/getDomainName.ts (or .js if you're not using TypeScript)

export const getDomainName = (url) => {
  try {
    const currentUrl = url || window.location.href;
    const { hostname } = new URL(currentUrl);
    return hostname;
  } catch (error) {
    console.error("Invalid URL:", error);
    return "";
  }
};
