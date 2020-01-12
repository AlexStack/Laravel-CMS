import { createBrowserHistory } from "history";
import { createStore, applyMiddleware, compose } from "redux";
import { createLogger } from "redux-logger";
import createRootReducer from "../reduxStores/Reducers/allReducers";
import { routerMiddleware } from "connected-react-router";

import { persistStore, persistReducer } from "redux-persist";
import storage from "redux-persist/lib/storage";

export const history = createBrowserHistory({ basename: "/point" });

const initialState = {
  //router: null,
  assignedTasks: [],
  login: {},
  myOwnTasks: {}
};

const persistConfig = {
  key: "root",
  storage,
  /**
   * Blacklist state that we do not need/want to persist
   */
  blacklist: ["router"]
};

const configureStore = (sagaMiddleware, storeHistory) => {
  //const sagaMiddleware = createSagaMiddleware()
  const reducers = createRootReducer(storeHistory);
  // console.log(reducers.router);
  const store = createStore(
    persistReducer(persistConfig, reducers), // root reducer with router state
    initialState,
    compose(
      applyMiddleware(
        routerMiddleware(storeHistory), // for dispatching history actions
        sagaMiddleware,
        createLogger()
      )
      // window.__REDUX_DEVTOOLS_EXTENSION__ &&
      //     window.__REDUX_DEVTOOLS_EXTENSION__()
      //DevTools.instrument()
    )
  );
  //sagaMiddleware.run(rootSaga)
  const persistor = persistStore(store);

  return { store, persistor };
};

export default configureStore;
