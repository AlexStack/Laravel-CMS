import React from "react";
import PropTypes from "prop-types";

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

  // tips: use [...store.pages.items] to clone to a new array with a new memory space
  // similar to clone a obj by Object.assign({}, store.pages.items)
  let filteredPages =
    filterKey == "newly_added"
      ? [...store.pages.items].sort((a, b) => b.id - a.id)
      : store.pages.items;

  filteredPages = filteredPages.filter(function(item, index) {
    if (searchKeyword) {
      const lowerKey = searchKeyword.toLowerCase();
      return (
        item.title.toLowerCase().includes(lowerKey) ||
        (item.menu_title && item.menu_title.toLowerCase().includes(lowerKey)) ||
        item.url
          .replace(/-|\.|\$/g, " ")
          .toLowerCase()
          .includes(lowerKey)
      );
    } else if (filterKey == "menu_enabled") {
      return item.menu_enabled;
    } else if (filterKey == "depth_1") {
      return item.depth < 1;
    } else if (filterKey == "depth_2") {
      return item.depth < 2;
    } else if (filterKey == "depth_3") {
      return item.depth < 3;
    } else if (filterKey == "newly_added") {
      return index < window.recently_added_numbers;
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
        totalNumber={store.pages.items.length}
      />

      <ul className="list-group search-results">
        {filteredPages.slice(0, window.display_limit_numbers).map(item => (
          <PageItem
            item={item}
            handleFieldChange={handleFieldChange}
            key={item.id}
            searchKeyword={searchKeyword}
            deleteConfirmId={deleteConfirmId}
          />
        ))}
      </ul>

      {filteredPages.length > window.display_limit_numbers && (
        <div className="col text-center mt-5">
          <i className="fas fa-exclamation-circle text-info mr-1" />
          Total page number is
          <span className="text-success p-2">{filteredPages.length}</span>, only
          display
          <span className="text-success p-2">
            {window.display_limit_numbers}
          </span>
          pages here for better performance. You can use the search form to find
          the specific pages or change the
          system.all_pages.display_limit_numbers setting to display more pages.
        </div>
      )}

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

const SearchForm = ({
  items,
  searchKeyword,
  handleFieldChange,
  formRef,
  filterKey,
  totalNumber
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
            placeholder={window.cmsLang.keyword}
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
            {!searchKeyword && totalNumber > window.display_option_numbers && (
              <select
                defaultValue={filterKey}
                onChange={handleFieldChange}
                name="filter_key"
                className="btn btn-outline-secondary"
              >
                <option value="all">{window.cmsLang.all_page}</option>
                <option value="menu_enabled">
                  {window.cmsLang.menu_enabled}
                </option>
                <option value="depth_1">Level 1</option>
                <option value="depth_2">Level 2</option>
                <option value="depth_3">Level 3</option>
                {/* <option value="all">{filterKey}</option> */}
                <option value="newly_added">
                  {window.cmsLang.recently_added}
                </option>
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
  let color_class = "";
  let icon = "";
  if (item.redirect_url || item.url.indexOf("http") === 0) {
    color_class = "text-success";
  }
  if (item.slug == "homepage" || item.url.indexOf("homepage") != -1) {
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
  if (item.slug == "homepage" || item.url.indexOf("homepage") != -1) {
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
        <i className="far fa-edit ml-3" />
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
        <a href={`./pages/create?parent_id=${item.id}&menu_enabled=0`}>
          <i className="far fa-plus-square ml-3" />
        </a>
      )}

      {item.url.indexOf("homepage") == -1 ? (
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
          {deleteConfirmId == item.id && (
            <span>{window.cmsLang.confirm_delete}</span>
          )}
        </a>
      ) : (
        <a
          className="btn btn-outline-primary btn-sm ml-3"
          href="./pages/create?switch_nav_tab=settings"
          role="button"
        >
          <i className="fas fa-plus-circle mr-1" />
          {window.cmsLang.create_new_page}
        </a>
      )}
    </li>
  );
};

AllPagePresent.propTypes = {
  store: PropTypes.object,
  handleFieldChange: PropTypes.func,
  formRef: PropTypes.object,
  searchKeyword: PropTypes.string,
  filterKey: PropTypes.string,
  deleteConfirmId: PropTypes.number
};

SearchForm.propTypes = {
  items: PropTypes.array,
  handleFieldChange: PropTypes.func,
  formRef: PropTypes.object,
  searchKeyword: PropTypes.string,
  filterKey: PropTypes.string,
  totalNumber: PropTypes.number
};

PageItem.propTypes = {
  item: PropTypes.object,
  handleFieldChange: PropTypes.func,
  formRef: PropTypes.object,
  searchKeyword: PropTypes.string,
  filterKey: PropTypes.string,
  deleteConfirmId: PropTypes.number
};

export default AllPagePresent;
