import axios from "axios";
import { API_URI } from "../constants/config";

const listAllPage = () => {
  return axios.get(`${API_URI}/pages`, {
    params: {
      keyword: "",
      tag: "",
      response_type: "json"
    }
  });
};

const deletePage = id => {
  return axios.delete(`${API_URI}/pages/${id}`, {
    params: {
      response_type: "json",
      _method: "DELETE"
    }
  });
};

const ApiMethods = {
  listAllPage,
  deletePage
};

export default ApiMethods;
