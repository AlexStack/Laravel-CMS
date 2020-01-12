// @flow

import { combineReducers } from "redux";
import { connectRouter } from "connected-react-router";

import allPageReducers from "./allPageReducers";

export default storeHistory =>
  combineReducers({
    router: connectRouter(storeHistory),
    pages: allPageReducers
  });
