$(function () {
  // $('.top-nav .dropdown-toggle').click(function () {
  //     location.href = $(this).attr('href');
  // });

  $('a').each(function () {
    if (this.href.match('_blank')) {
      $(this).attr('target', '_blank');
    }
  });
});
