/* eslint-disable no-undef */
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

function submitInquiryForm() {
  if (document.querySelector("#laravel-cms-inquiry-form") == null) {
    return false;
  }
  $("#laravel-cms-inquiry-form").submit(function (event) {
    event.preventDefault();
    if (
      typeof grecaptcha != "undefined" &&
      $("div.g-recaptcha").data("sitekey") != null
    ) {
      var response = grecaptcha.getResponse();
      if (response.length == 0) {
        //alert('Google recaptcha not ticked, no ajax');
        return false;
      }
    }

    $('#laravel-cms-inquiry-form button[type="submit"]')
      .attr("disabled", "disabled")
      .append('<i class="fas fa-spinner fa-spin ml-2"></i>');

    $.ajax({
      url: $(this).attr("action"),
      type: $(this).attr("method"),
      data: new FormData(this),
      contentType: false,
      cache: false,
      processData: false,
      dataType: "json",
      success: function (data) {
        //console.log("Submission was successful.");
        //console.log(data);
        if (data.success) {
          $("#laravel-cms-inquiry-form .form-group").fadeOut("slow");
          $("#laravel-cms-inquiry-form-results").html(data.success_content);
        } else {
          $("#laravel-cms-inquiry-form .error_message").html(
            "Error: " + data.error_message
          );

          $('#laravel-cms-inquiry-form button[type="submit"]').removeAttr(
            "disabled"
          );
          $(
            '#laravel-cms-inquiry-form button[type="submit"] i.fa-spinner'
          ).remove();
        }
      },
      error: function (data) {
        $("#laravel-cms-inquiry-form .error_message").html(
          "Error: " + data.responseJSON.message
        );
        $('#laravel-cms-inquiry-form button[type="submit"]').removeAttr(
          "disabled"
        );
        $(
          '#laravel-cms-inquiry-form button[type="submit"] i.fa-spinner'
        ).remove();

        //console.log("laravel-cms-inquiry-form : An error occurred.");
        //console.log(data);
      }
    }).done(function (data) {
      // console.log("laravel-cms-inquiry-form submitted");
      //console.log(data);
    });
  });
}


$(function () {
  submitInquiryForm();

  adjustAllLinks();

  loadPrettifyJs();
});
