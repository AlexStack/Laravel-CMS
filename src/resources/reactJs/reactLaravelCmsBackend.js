import React, { Component } from "react";
import ReactDOM from "react-dom";
import Root from "./containers/Root";
import configureStore, {
  history as storeHistory
} from "./reduxStores/configureStore";
import rootSaga from "./middlewares/saga";
import createSagaMiddleware from "redux-saga";

const sagaMiddleware = createSagaMiddleware();
const { store, persistor } = configureStore(sagaMiddleware, storeHistory);
sagaMiddleware.run(rootSaga);

export default class App extends Component {
  render() {
    return (
      <Root store={store} persistor={persistor} storeHistory={storeHistory} />
    );
    // return <span>this is a test, just output a render for the 1st try</span>;
  }
}

/**
 * First we will load all of this project's JavaScript dependencies which
 * includes React and other helpers. It's a great starting point while
 * building robust, powerful web applications using React + Laravel.
 */

// require("./bootstrap");

if (document.getElementById("react-js-sap")) {
  ReactDOM.render(<App />, document.getElementById("react-js-sap"));
}
