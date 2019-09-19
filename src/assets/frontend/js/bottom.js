function loadPrettifyJs(force_load) {
  if (
    typeof force_load != "undefined" ||
    document.querySelector(".prettyprint") !== null
  ) {
    // use appendChild() as $.getScript() will cause no cache
    var tag = document.createElement("script");
    tag.src =
      "https://cdn.jsdelivr.net/gh/google/code-prettify@master/loader/run_prettify.js";
    document.getElementsByTagName("head")[0].appendChild(tag);
    return true;
  }
  return false;
}

function adjustAllLinks() {
  // $('.top-nav .dropdown-toggle').click(function () {
  //     location.href = $(this).attr('href');
  // });

  $("a").each(function () {
    if (this.href.match("_blank")) {
      $(this).attr("target", "_blank");
    }
  });
}

$(function () {
  adjustAllLinks();

  loadPrettifyJs();
});
