import React from "react";
import PropTypes from "prop-types";
import { Lang, POINT_FUNC } from "../constants/config";

const AllPagePresent = ({
  store,
  handleFieldChange,
  formRef,
  searchKeyword,
  filterKey,
  deleteConfirmId
}) => {
  // return <span>AllPagePresent AllPagePresent {store.pages.items.length}</span>;
  if (filterKey == "all") {
    filterKey = store.pages.filterKey;
  }

  // let filteredPages = Object.assign({}, store.pages.items); // store.pages.items is an array, can not use object.assign for sort()

  // let tmpObj = [...store.pages.items];
  // console.log(tmpObj);
  // tmpObj = tmpObj.sort((a, b) => a.id < b.id);

  // tips: use [...store.pages.items] to clone to a new array with a new memory space
  // similar to clone a obj by Object.assign({}, store.pages.items)
  let filteredPages =
    filterKey == "newly_added"
      ? [...store.pages.items].sort((a, b) => a.id < b.id)
      : store.pages.items;

  filteredPages = filteredPages.filter(function(item, index) {
    if (searchKeyword) {
      return item.title.toLowerCase().includes(searchKeyword.toLowerCase());
    } else if (filterKey == "menu_enabled") {
      return item.menu_enabled;
    } else if (filterKey == "depth_1") {
      return item.depth < 1;
    } else if (filterKey == "depth_2") {
      return item.depth < 2;
    } else if (filterKey == "depth_3") {
      return item.depth < 3;
    } else if (filterKey == "newly_added") {
      return index < 50;
    }
    return true;
  });

  return (
    <div className="container-fluid main-content">
      <SearchForm
        items={filteredPages}
        handleFieldChange={handleFieldChange}
        searchKeyword={searchKeyword}
        formRef={formRef}
        filterKey={store.pages.filterKey}
      />

      <ul className="list-group search-results">
        {filteredPages.map(item => (
          <PageItem
            item={item}
            handleFieldChange={handleFieldChange}
            key={item.id}
            searchKeyword={searchKeyword}
            deleteConfirmId={deleteConfirmId}
          />
        ))}
      </ul>

      {!searchKeyword && filteredPages.length == 0 && (
        <div className="row">
          <div className="col text-center mt-5">
            <i className="fas fa-spinner fa-spin text-success mr-1" /> Loading
            data ...
          </div>
        </div>
      )}

      {searchKeyword && filteredPages.length == 0 && (
        <div className="col text-center mt-5">
          <i className="fas fa-exclamation-circle text-info mr-1" />
          No search result
        </div>
      )}
    </div>
  );
};
AllPagePresent.propTypes = {
  store: PropTypes.object,
  myPoints: PropTypes.object,
  modalData: PropTypes.object,
  totalPoints: PropTypes.number,
  handleFieldChange: PropTypes.func,
  handleAskQuestion: PropTypes.func
};

const SearchForm = ({
  items,
  searchKeyword,
  handleFieldChange,
  formRef,
  filterKey
}) => {
  return (
    <form id="page-search-form" ref={formRef}>
      <div className="row justify-content-center">
        <div className="input-group col-md-8 mb-2">
          <input
            type="text"
            className="form-control"
            name="search_keyword"
            onChange={handleFieldChange}
            // value={searchKeyword || ""}
            placeholder="Keyword"
            disabled={!searchKeyword && items.length == 0}
          />
          <div className="input-group-append">
            {searchKeyword || items.length > 0 ? (
              <button
                type="reset"
                id="btn_result"
                className="btn btn-outline-secondary text-dark"
                disabled="disabled"
              >
                <i className="fas fa-atlas mr-1" /> {items.length}
              </button>
            ) : (
              <button type="reset" className="btn btn-secondary">
                <i className="fas fa-search mr-1" />
              </button>
            )}
            {!searchKeyword && (
              <select
                defaultValue={filterKey}
                onChange={handleFieldChange}
                name="filter_key"
                className="btn btn-outline-secondary"
              >
                <option value="all">All</option>
                <option value="menu_enabled">Menu Enabled</option>
                <option value="depth_1">Level 1</option>
                <option value="depth_2">Level 2</option>
                <option value="depth_3">Level 3</option>
                {/* <option value="all">{filterKey}</option> */}
                <option value="newly_added">Recently Added</option>
              </select>
            )}

            {searchKeyword && (
              <button
                type="button"
                onClick={handleFieldChange}
                name="clear_keyword"
                className="btn btn-secondary"
              >
                <i className="fas fa-undo" id="clear_keyword" />
              </button>
            )}
          </div>
        </div>
      </div>
    </form>
  );
};

const PageItem = ({
  item,
  searchKeyword,
  handleFieldChange,
  deleteConfirmId
}) => {
  const depthStr = "⎯⎯⎯";
  let color_class = "text-secondary";
  let icon = "";
  if (item.redirect_url) {
    color_class = "text-success";
  }
  if (item.slug == "homepage") {
    color_class = "text-primary";
  }
  if (item.menu_enabled) {
    if (item.depth == 0) {
      icon = "fas fa-list-alt mr-1 " + color_class + "";
    } else if (item.depth == 1) {
      icon = "fas fa-list-ul mr-1 " + color_class + "";
    } else {
      icon = "fas fa-stream mr-1 " + color_class + "";
    }
  } else {
    icon = "far fa-file mr-1 " + color_class + "";
  }
  if (item.slug == "homepage") {
    icon = "fas fa-home mr-1 " + color_class + "";
  }

  return (
    <li className="list-group-item list-group-item-action">
      <i className={icon} />
      {!searchKeyword && depthStr.repeat(item.depth) + " "}
      <a
        href={`./pages/${item.id}/edit`}
        className={item.menu_enabled && "menu_enabled"}
      >
        {item.menu_title && `[${item.menu_title}] `}
        {/* {item.title && searchKeyword
          ? item.title.replace(searchKeyword, "*" + searchKeyword + "*")
          : item.title} */}
        {item.title}
      </a>

      <a
        href={item.url}
        className={color_class}
        target="_blank"
        rel="noreferrer noopener"
      >
        <i className="far fa-eye ml-3" />
      </a>

      {item.menu_enabled && (
        <a
          href={`./pages/create?parent_id=${item.id}&meu_enabled=0`}
          className="text-secondary"
        >
          <i className="far fa-plus-square ml-3" />
        </a>
      )}

      {item.slug != "homepage" && (
        <a
          className={`float-right ${
            deleteConfirmId == item.id ? "text-danger" : "delete-link"
          }`}
          href="###"
          onClick={e => {
            handleFieldChange(e, "delete_item", item);
          }}
        >
          <i className="far fa-trash-alt mr-1" />
          {deleteConfirmId == item.id && <span>Confirm Delete</span>}
        </a>
      )}
    </li>
  );
};

export default AllPagePresent;
