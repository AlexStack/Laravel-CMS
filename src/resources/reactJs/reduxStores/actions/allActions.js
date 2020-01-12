import { createActions } from "reduxsauce";

/* ------------- Types and Action Creators ------------- */

const { Types, Creators } = createActions({
  listAllPageRequest: [null],
  listAllPageSuccess: ["items"],
  listAllPageFailure: ["error"],

  deletePageRequest: ["id"],
  deletePageSuccess: ["id", "page"],
  deletePageFailure: ["error"],

  setFilterKeyRequest: ["value"],
  setFilterKeySuccess: ["value"],
  setFilterKeyFailure: ["error"]
});

export const actionTypes = Types;
export const taskTypes = Types;
export default Creators;
