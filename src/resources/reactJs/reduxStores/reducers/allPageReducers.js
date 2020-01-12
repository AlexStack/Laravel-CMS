import { createReducer } from "reduxsauce";
import { actionTypes } from "../actions/allActions";

// the initial state of this reducer
export const INITIAL_STATE = {
  items: [],
  errorMsg: null,
  formStatus: null, // Submitting, Success, Failed
  applyTask: null,
  histories: [],
  filterKey: "all"
};

const listAllPages = (state = INITIAL_STATE, action) => {
  return { ...state, items: action.items };
};

const deletePage = (state = INITIAL_STATE, action) => {
  // console.log(action, action, action);
  return {
    ...state,
    items: state.items.filter(item => item.id !== action.id)
  };
};

const setFilterKey = (state = INITIAL_STATE, action) => {
  return { ...state, filterKey: action.value };
};

// map our action types to our reducer functions
export const HANDLERS = {
  [actionTypes.LIST_ALL_PAGE_SUCCESS]: listAllPages,
  [actionTypes.DELETE_PAGE_SUCCESS]: deletePage,
  [actionTypes.SET_FILTER_KEY_SUCCESS]: setFilterKey
  // [actionTypes.APPLY_TASK_GROUP_FAILURE]: applyTaskFailure
};

export default createReducer(INITIAL_STATE, HANDLERS);
