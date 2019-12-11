import { createReducer } from "reduxsauce";
import { actionTypes } from "../actions/allActions";

// the initial state of this reducer
export const INITIAL_STATE = {
  items: [],
  errorMsg: null,
  formStatus: null, // Submitting, Success, Failed
  applyTask: null,
  histories: [],
  questionForm2: null
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

// map our action types to our reducer functions
export const HANDLERS = {
  [actionTypes.LIST_ALL_PAGE_SUCCESS]: listAllPages,
  [actionTypes.DELETE_PAGE_SUCCESS]: deletePage
  // [actionTypes.APPLY_TASK_GROUP_FAILURE]: applyTaskFailure
};

export default createReducer(INITIAL_STATE, HANDLERS);
