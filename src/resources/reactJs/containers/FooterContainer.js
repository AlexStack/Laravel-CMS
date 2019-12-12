import React, { Component } from "react";
import { connect } from "react-redux";
// import PropTypes from "prop-types";
import FooterPresent from "../components/FooterPresent";

class FooterContainer extends Component {
  constructor(props) {
    super(props);
  }
  render() {
    return <FooterPresent />;
  }
}

const mapStateToProps = () => {
  //console.log("mapStateToProps FooterContainer", state.login);
  return {
    // login: state.login
  };
};

FooterContainer.propTypes = {
  // login: PropTypes.object
};

export default connect(
  mapStateToProps,
  null
)(FooterContainer);
