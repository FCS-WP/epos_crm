import { makeRequest } from "./axios";

export const webApi = {
  
  async getConfigs(params) {
    return await makeRequest("/configs", params);
  },

  async loginAccount(params) {
    return await makeRequest("/customers/login", params, "POST");
  },
  async getCustomer(params) {
    return await makeRequest("/customers", params, "GET");
  },
  async updateAccount(params) {
    return await makeRequest("/customers", params, "PATCH");
  },
  async registerAccount(params) {
    return await makeRequest("/customers/register", params, "POST");
  },

   async pointRedeem(params) {
    return await makeRequest("/customers/redeem", params, "POST");
  },
};
