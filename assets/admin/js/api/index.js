import { makeRequest } from "./axios";
import { eposRequest } from "./epos-api";
export const Api = {
  async checkKeyExits(params) {
    return await makeRequest("/check_option", params);
  },
};

export const eposApi = {
  async connect(params) {
    return await eposRequest("/connect", params);
  },
}
