import { Http } from "@/utils/Http";
import init from "@/utils/Http/init";

const http = new Http(init);

export const saveMemorandum = async (payload: any) => {
  try {
    const response = await http.post("oac/memorandum", payload);
    return response.data;
  } catch (error) {
    throw error;
  }
};
