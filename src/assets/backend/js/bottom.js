/* eslint-disable no-undef */
function renderEditor(id, minHeight = 120) {
  $(id).summernote({
    placeholder: '',
    tabsize: 2,
    minHeight: minHeight,
    maxHeight: 600
  });
}
$(document).ready(function () {
  renderEditor('#main_content', 200);
  setTimeout(function () {
    renderEditor('#sub_content');
  }, 1500);

  setTimeout(function () {
    renderEditor('#abstract');
    renderEditor('#extra_content_1');
    renderEditor('#extra_content_2');
    renderEditor('#extra_content_3');
  }, 3000);

  setTimeout(function () {
    renderEditor('#success_content');
  }, 4000);
});
