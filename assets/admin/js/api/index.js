import { makeRequest } from "./axios";
import { eposRequest } from "./epos-api";
export const Api = {
  async checkKeyExits(params) {
    return await makeRequest("/get-options", params, "POST");
  },
  async updateKeys(params) {
    return await makeRequest("/update-options", params, "POST");
  },
};

export const eposApi = {
  async connect(params) {
    return await eposRequest("/connect", params);
  },
}
