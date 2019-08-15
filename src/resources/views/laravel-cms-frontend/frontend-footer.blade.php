<div class="container">
    {!! config('laravel-cms.page_footer') !!}
</div>


<script>
$(function(){
    // $('.top-nav .dropdown-toggle').click(function () {
    //     location.href = $(this).attr('href');
    // });

    $('.top-nav a').each(function() {
        if ( this.href.match( '_blank' ) ){
            $(this).attr('target',  '_blank');
        }
    });
});
</script>
