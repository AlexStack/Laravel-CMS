/* eslint-disable no-undef */
function renderEditor(id, minHeight = 120) {
  $(id).summernote({
    placeholder: "",
    tabsize: 2,
    minHeight: minHeight,
    maxHeight: 600
  });
}
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
});


function insertImageToEditor(editor_id, img_url) {
  $(editor_id).summernote('insertImage', img_url, function ($image) {
    $image.css('border', 0);
    $image.attr('class', 'img-fluid');
  });
}
