import { call, put, takeLatest } from "redux-saga/effects";
import Api from "./apiServices";
// import { push } from "connected-react-router";
import allActions, { actionTypes } from "../reduxStores/actions/allActions";
import { myLog } from "../constants/config";

function* listAllPage(action) {
  try {
    const json = yield call(Api.listAllPage);
    myLog("listAllPage-mylog", action.type, json);
    yield put(allActions.listAllPageSuccess(json.data));
  } catch (e) {
    yield put(allActions.listAllPageFailure(e.message));
  }
}

function* deletePage(action) {
  try {
    const json = yield call(Api.deletePage, action.id);
    myLog(action, json);
    yield put(allActions.deletePageSuccess(action.id));
    // yield put(push("/listPage"));
  } catch (e) {
    yield put(allActions.deletePageFailure(e.message));
  }
}

function* setFilterKey(action) {
  try {
    yield put(allActions.setFilterKeySuccess(action.value));
  } catch (e) {
    yield put(allActions.setFilterKeyFailure(e.message));
  }
}

function* rootSaga() {
  yield takeLatest(actionTypes.LIST_ALL_PAGE_REQUEST, listAllPage);

  yield takeLatest(actionTypes.DELETE_PAGE_REQUEST, deletePage);

  yield takeLatest(actionTypes.SET_FILTER_KEY_REQUEST, setFilterKey);
}

export default rootSaga;
