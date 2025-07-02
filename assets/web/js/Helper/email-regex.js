export const emailRegex = /^[^@]+@[^@]+\.[^@]+$/;

export const isValidEmail = (email) => {
  const regex = /^[^@]+@[^@]+\.[^@]+$/;
  return regex.test(email);
};
