/* eslint-disable react/prop-types */
import React from "react";
import PropTypes from "prop-types";
import { Provider } from "react-redux";
import { Route, Switch } from "react-router-dom";
import { ConnectedRouter } from "connected-react-router";
import { PersistGate } from "redux-persist/integration/react";

import AllPageContainer from "./AllPageContainer";
import FooterContainer from "./FooterContainer";

const Root = ({ store, persistor, storeHistory }) => (
  <Provider store={store}>
    <PersistGate loading={null} persistor={persistor}>
      <ConnectedRouter history={storeHistory}>
        <div className="container-fluid top-container">
          {/* <HeaderContainer /> */}
          <Switch>
            <Route path="/" component={AllPageContainer} />
          </Switch>
          {/* <DevTools /> */}
          <FooterContainer />
        </div>
      </ConnectedRouter>
    </PersistGate>
  </Provider>
);

Root.propTypes = {
  store: PropTypes.object.isRequired
};

export default Root;
