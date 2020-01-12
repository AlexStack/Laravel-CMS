/* eslint-disable no-undef */
import React, { Component } from "react";
import { connect } from "react-redux";
import PropTypes from "prop-types";
import AllPagePresent from "../components/AllPagePresent";
import allActions from "../reduxStores/actions/allActions";
// import Cookies from "js-cookie";

class AllPageContainer extends Component {
  constructor(props) {
    super(props);

    this.state = {
      searchKeyword: null,
      filterKey: "all",
      deleteConfirmId: null
    };

    this.formRef = React.createRef();

    this.handleFieldChange = this.handleFieldChange.bind(this);
    this.handleFieldChangeNow = _.debounce(this.handleFieldChangeNow, 600);
  }

  componentDidMount() {
    this.props.dispatch(allActions.listAllPageRequest());
  }

  componentDidUpdate() {
    // console.log("componentDidUpdate", this.state);
  }

  totalPointAnimation = (sum, previousPoints) => {
    let css = "text-success";
    if (sum > previousPoints) {
      css = "text-success";
    } else if (sum < previousPoints) {
      css = "text-primary";
    } else {
      css = "text-danger";
    }

    $("#btn_result")
      .removeClass("text-dark")
      .addClass(css);
    setTimeout(() => {
      $("#btn_result")
        .removeClass(css)
        .addClass("text-dark");
    }, 3000);
  };

  handleFieldChange = (e, action, item) => {
    e.persist();
    this.handleFieldChangeNow(e, action, item);
  };

  handleFieldChangeNow(event, action, item) {
    // console.log(this.formRef, action);
    // console.log(event, event.target.name, event.target.value);
    if (event.target.name == "search_keyword") {
      this.setState({
        searchKeyword: event.target.value
      });
      this.totalPointAnimation(2, 3);
      $(".search-results").unmark({
        done: function() {
          $(".search-results").mark(event.target.value);
        }
      });
    } else if (
      event.target.name == "clear_keyword" ||
      event.target.id == "clear_keyword"
    ) {
      this.setState({
        searchKeyword: null,
        filterKey: "all"
      });
      if (this.formRef.current.elements[0].name == "search_keyword") {
        this.formRef.current.elements[0].value = "";
      }
    } else if (event.target.name == "filter_key") {
      this.setState({
        filterKey: event.target.value
      });
      this.props.dispatch(allActions.setFilterKeyRequest(event.target.value));

      this.totalPointAnimation(2, 1);
      this.formRef.current.elements[0].focus();
      this.formRef.current.elements[0].blur();
    } else if (action == "delete_item") {
      event.preventDefault();
      if (this.state.deleteConfirmId == item.id) {
        // alert("can delete");
        this.props.dispatch(allActions.deletePageRequest(item.id));
      }
      this.setState({
        deleteConfirmId: item.id
      });
    }
  }

  render() {
    return (
      <AllPagePresent
        store={this.props}
        handleFieldChange={this.handleFieldChange}
        formRef={this.formRef}
        searchKeyword={this.state.searchKeyword}
        filterKey={this.state.filterKey}
        deleteConfirmId={this.state.deleteConfirmId}
      />
    );
  }
}

const mapStateToProps = state => {
  //console.log("mapStateToProps AllPageContainer", state);
  return state;
};

// const mapDispatchToProps = dispatch => {
//   return {
//     // askQuestion: newDate => {
//     //     dispatch(allActions.askQuestionRequest(newDate));
//     // },
//   };
// };

AllPageContainer.propTypes = {
  dispatch: PropTypes.func.isRequired
};

export default connect(
  mapStateToProps,
  null
)(AllPageContainer);
