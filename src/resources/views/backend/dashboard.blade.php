@extends('laravel-cms::' . $helper->s('template.backend_dir') . '.includes.layout')

@section('content')

<div class="container">
    <div class="row justify-content-center mt-2">
        <div class="col-md-auto text-center">
            <h1>{{ $helper->t('dashboard')}}</h1>


            {!! $helper->t('cms_version_notice', [
            'current'=>'<span class="text-success font-weight-bold current_version">' . $cms_version .'</span>',
            'latest'=>'<span class="text-danger font-weight-bold latest_version">***</span>'
            ]) !!}
        </div>
    </div>
</div>

<script>
    var cmsGitHubTags = $.getJSON( "https://api.github.com/repos/AlexStack/Laravel-CMS/tags", function(data) {
        console.log( "success" );
        console.log(data[0]['name']);
    })
    .done(function(data) {
        console.log( "second success");
        console.log(data[0]['name']);
        $('span.latest_version').html('' + data[0]['name']);
    })
    .fail(function() {
        console.log( "error" );
    })
    .always(function() {
        console.log( "complete" );
    });
</script>

@endsection
