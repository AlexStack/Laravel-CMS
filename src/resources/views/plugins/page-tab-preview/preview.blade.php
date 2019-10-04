@php
if ( !isset($page)) {
$page_url = '';
} else {
$page_url = $helper->url($page);
}

//$purge_url = '/purge_noargs' . $page_url;
@endphp

<div class="row  justify-content-center">
    <div class="col-md-4 text-right">
        <a class="btn btn-secondary mb-3 mt-3 random-param" href="{{$page_url}}" target="mobile_iframe">Mobile
            Preview</a>
        <br />
        <a class="btn btn-info mb-3 mt-3 random-param" href="{{$page_url}}" target="_blank">Desktop Preview</a>
        <br />
        {{-- purge nginx cache link --}}
        @if ( $helper->s('plugin.page-tab-preview.purge_prefix') )
        <a class="btn btn-warning mb-3 mt-3" href="{{$helper->s('plugin.page-tab-preview.purge_prefix') . $page_url}}"
            target="mobile_iframe">Purge Page Cache</a>
        <br />
        @endif

    </div>
    <div class="col-md-7 no_iframe" id="iframe_div"></div>
</div>

<div class="mb-5"></div>

@if ( isset($page) )
<script>
    var heartbeatInterval = setInterval(function() {
        if ( $('#iframe_div').hasClass('no_iframe') && $('#preview').hasClass('active') ){
            $('#iframe_div').html('<iframe src="{{$page_url}}" name="mobile_iframe" frameborder="0"  style="width:400px;height:600px;"></iframe>');
            $('#iframe_div').removeClass('no_iframe');
        }
    }, 1000);

    // to avoid browser cache
    $('a.random-param').click(function () {
        $(this).attr('href',"{{$page_url}}?t=" + Math.random());
    });
</script>
@else
<script>
    $('.nav-tabs .nav-link').each(function () {
        if ($(this).attr('href') == "#preview") {
            $(this).addClass('disabled');
        }
    });
</script>
@endif
