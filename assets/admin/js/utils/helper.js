export const getDomainName = (url) => {
  try {
    const currentUrl = url || window.location.href;
    const { protocol, hostname, port } = new URL(currentUrl);
    return `${protocol}//${hostname}${port ? `:${port}` : ""}`;
  } catch (error) {
    console.error("Invalid URL:", error);
    return "";
  }
};
