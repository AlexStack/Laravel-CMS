/* eslint-disable no-unused-vars */
/* eslint-disable no-undef */

function renderEditor(id, minHeight = 120) {
  $(id).summernote({
    placeholder: "",
    tabsize: 2,
    minHeight: minHeight,
    maxHeight: 450,
    toolbar: [
      //['img', ['picture']],
      ["style", ["style", "clear"]],
      [
        "fontstyle",
        [
          "bold",
          "italic",
          "strikethrough",
          "underline",
          "ul",
          "ol",
          "paragraph"
        ]
      ],
      ["fontstyleextra", ["hr", "link", "color", "superscript", "subscript"]],
      ["extra", ["video", "table", "height"]],
      ["misc", ["undo", "redo", "fullscreen", "codeview", "highlight"]]
    ]
  });

  var label_class = id.replace("textarea.input-", "label.label-");
  var browser_img_url =
    admin_route + "/files?insert_files_to_editor=1&editor_id=" + id;
  var browser_img_str =
    '<a href="' +
    browser_img_url +
    '" target="_blank" class="ml-2 text-info show-iframe-modal" onclick="return showIframeModal(\'' +
    browser_img_url +
    '\');" title="Insert File"><i class="far fa-images"></i></a>';

  $(label_class).html($(label_class).html() + browser_img_str);
}

function insertImageToEditor(editor_id, img_url) {
  var HTMLstring =
    '<img src="' + img_url + '" class="img-fluid content-img" />';
  $(editor_id).summernote("pasteHTML", HTMLstring);

  // $(editor_id).summernote("insertImage", img_url, function ($image) {
  //   $image.css("border", 0);
  //   $image.attr("class", "img-fluid");
  // });
}

function insertHtmlToEditor(editor_id, html_str) {
  $(editor_id).summernote("pasteHTML", html_str);
}

function showIframeModal(url, modal_id) {
  if (typeof modal_id == "undefined") {
    modal_id = "#iframe-modal";
  }
  if (
    $(modal_id + " iframe").attr("src").indexOf(url) == -1
  ) {
    $(modal_id + " iframe").attr("src", url);
    $(modal_id + " iframe").addClass("iframe-loaded");
  }
  $(modal_id).modal("show");
  return false;
}

function hideIframeModal(modal_id) {
  if (typeof modal_id == "undefined") {
    modal_id = "#iframe-modal";
  }
  $(modal_id).modal("hide");
  return false;
}

function switchNavTab(nav_tab_id) {
  if (nav_tab_id == "") {
    return false;
  }
  $('.nav-tabs a[href="#' + nav_tab_id + '"]').trigger("click");
  $('#page_content_form button[type="submit"]').click(function (e) {
    if ($("#page_content_form .input-title").val() == "") {
      $('.nav-tabs a[href="#main-content"]').trigger("click");
      return false;
    }
    return true;
  });
}

function sortableList(list_id) {
  $("#sortableList").sortable({
    handle: ".handle", // handle's class
    animation: 150,
    // Element dragging ended
    onEnd: function ( /**Event*/ evt) {
      var itemEl = evt.item; // dragged HTMLElement
      evt.to; // target list
      evt.from; // previous list
      evt.oldIndex; // element's old index within old parent
      evt.newIndex; // element's new index within new parent
      evt.oldDraggableIndex; // element's old index within old parent, only counting draggable elements
      evt.newDraggableIndex; // element's new index within new parent, only counting draggable elements
      evt.clone; // the clone element
      evt.pullMode; // when item is in another sortable: `"clone"` if cloning, `true` if moving
      //console.log(evt);
    }
  });
}




function adjustSmallScreen() {
  if ($(window).width() > 700) {
    return false;
  }
  $('.menu-links .btn').addClass('btn-sm text-truncate').css('width', '2rem');
  $('.menu-links .btn i').addClass('pr-5');
  $('.menu-links').addClass('pl-0 pr-1').css('overflow', 'visible');
  $('.menu-logo').addClass('pr-0');
  $('.latest-settings').addClass('order-last');
  $('.save-buttons .btn').addClass('btn-sm mr-1').removeClass('mr-3');
  $('.save-buttons a.text-secondary').removeClass('ml-3');

  $('#iframe-modal .embed-responsive').css('height', '600px');
}




// Implement functions when document is ready

$(document).ready(function () {
  renderEditor("textarea.input-main_content", 200);
  setTimeout(function () {
    renderEditor("textarea.input-sub_content");
  }, 1500);

  setTimeout(function () {
    renderEditor("textarea.input-abstract");
    renderEditor("textarea.input-extra_content_1");
    renderEditor("textarea.input-extra_content_2");
    renderEditor("textarea.input-extra_content_3");
  }, 3000);

  setTimeout(function () {
    renderEditor("textarea.input-success_content");
  }, 4000);

  adjustSmallScreen();
});
