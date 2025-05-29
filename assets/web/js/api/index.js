import { makeRequest } from "./axios";

export const webApi = {
  
  async getConfigs(params) {
    return await makeRequest("/configs", params);
  },

  async loginAccount(params) {
    return await makeRequest("/customers/login", params, "POST");
  },
  async registerAccount(params) {
    return await makeRequest("/customers/register", params, "POST");
  },
};
