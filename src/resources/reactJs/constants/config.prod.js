/* eslint-disable no-console */
// import React from "react";
// import { Field, ErrorMessage } from "formik";
import LanguageStrings from "./LanguageStrings";

const reactLanguage = window.reactLanguage ? window.reactLanguage : "en";

LanguageStrings.setLanguage(reactLanguage);
export const Lang = LanguageStrings;

let api_uri = window.admin_route;
let project_name = "Laravel CMS";
let project_name_plural = "Laravel CMS";
// if (window.location.href.indexOf(".test") != -1) {
//   api_uri = "http://react.js.test:9192/api";
// } else if (window.location.href.indexOf("127.0.0.1") != -1) {
//   api_uri = "/api";
// }

export const DATE_FORMATE = "DD/MM/YYYY";

export const API_URI = api_uri;
export const WWW_SITE = "...";
export const MEMBER_SITE = "...";

export const POINT_FUC = (num = 1) =>
  num > 1 || num < -1 ? Lang.points : Lang.point;
export const PROJECT_NAME_FUC = (num = 1) =>
  num > 1 ? project_name_plural : project_name;
export const PROJECT_NAME = project_name;
export const PROJECT_NAME_PLURAL = project_name_plural;

export const myLog = variable => {
  console.log(variable);
};

export const POINT_FUNC = (num = 1, lang = "english") => {
  if (lang == "english") {
    return num > 1 || num < -1 ? Lang.points : Lang.point;
  } else if (lang == "chinese") {
    return num > 1 || num < -1 ? Lang.points : Lang.point;
  }
};
