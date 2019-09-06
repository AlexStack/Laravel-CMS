@php
    $page_url = $helper->url($page_model);
    $purge_url = '/purge_noargs' . $page_url;
@endphp

<div class="row  justify-content-center">
    <div class="col-md-5 text-right">
        <a class="btn btn-secondary mb-3 mt-3" href="{{$page_url}}" target="mobile_iframe">Mobile Preview</a>
        <br/>
        <a class="btn btn-info mb-3 mt-3" href="{{$page_url}}" target="_blank">Desktop Preview</a>
        <br/>
        <a class="btn btn-warning mb-3 mt-3" href="{{$purge_url}}" target="mobile_iframe">Purge Page Cache</a>
        <br/>
        <script>
        var heartbeatInterval = setInterval(function() {
            if ( $('#iframe_div').hasClass('no_iframe') && $('#preview').hasClass('active') ){
                $('#iframe_div').html('<iframe src="{{$page_url}}" name="mobile_iframe" frameborder="0"  style="width:400px;height:600px;"></iframe>');
                $('#iframe_div').removeClass('no_iframe');
            }
        }, 1000);
        </script>
    </div>
    <div class="col-md-7 no_iframe" id="iframe_div"></div>
</div>

<div class="mb-5"></div>
