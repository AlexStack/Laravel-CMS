<!doctype html>
<html lang="{{$helper->s('template.backend_language')}}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $helper->s('site_name') ?? 'Amila Laravel CMS'}} Backend</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.11.2/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.4.1/dist/jquery.min.js" crossorigin="anonymous"></script>


    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote-bs4.css" rel="stylesheet">

    <link rel="icon" href="{{$helper->assetUrl('images/favicon-32x32.png', false, true) }}" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote-bs4.js"></script>

    <link href="{{$helper->assetUrl('css/main.css', true, true) }}" rel="stylesheet">
    <script>
        var admin_route = "{{config('laravel-cms.admin_route')}}";
        var admin_role  = "{{$helper->user->laravel_cms_admin_role}}";
    </script>
</head>

<body
    class="{{str_replace(['.','LaravelCmsAdmin'],[' ','LaravelCmsAdmin '],request()->route()->getName())}} {{request()->route()->parameters['plugin'] ?? ''}}">

    @include($helper->bladePath('includes.header','b'))
    @yield('content')

    <!-- iframe-modal -->
    <div id="iframe-modal" class="modal fade " tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="iframe-modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0 m-0">
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe class="embed-responsive-item" src="data:text/html,Loading..." id="modal-iframe"
                            data-loaded="iframe-modal-loaded"></iframe>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include($helper->bladePath('includes.footer','b'))

</body>

</html>
