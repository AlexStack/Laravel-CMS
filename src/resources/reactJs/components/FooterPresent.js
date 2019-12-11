import React from "react";
// import { myLog } from "../constants/config";
import PropTypes from "prop-types";

const FooterPresent = () => {
  //myLog(["FooterPresent", user]);
  return (
    <div className="mt-5 mb-5 pt-5 pb-5 pl-4 bg-light text-dark">
      {/* Milliseconds {new Date().getMilliseconds()} */}
    </div>
  );
};

FooterPresent.propTypes = {
  user: PropTypes.object
};
export default FooterPresent;
