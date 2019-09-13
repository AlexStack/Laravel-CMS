<!doctype html>
<html lang="{{$helper->s('template.frontend_language')}}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        {{$page->meta_title ?? $page->title}}{{strlen($page->meta_title ?? $page->title) < 40 ? ' - ' . $helper->s('site_name') : ''}}
    </title>
    @if ( $page->meta_keywords )
    <meta name="keywords" content="{{$page->meta_keywords}}" />
    @endif
    @if ( $page->meta_description )
    <meta name="description" content="{{$page->meta_description}}" />
    @endif

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.9.0/css/all.min.css">

    <link rel="icon" href="{{ $helper->s('favicon_url') ?? 'favicon.ico'}}" />

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.4.1/dist/jquery.min.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="{{ $helper->assetUrl('css/main.css') }}">

</head>

<body class="{{$page->template_file}} page-{{$page->id}} slug-{{str_replace('.html', '', $page->slug) }}">
    @include('laravel-cms::' . $helper->s('template.frontend_dir') . '.includes.header')

    @yield('content')

    @include('laravel-cms::' . $helper->s('template.frontend_dir') . '.includes.footer')

    <script src="{{ $helper->assetUrl('js/bottom.js') }}"></script>


    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.15.0/dist/umd/popper.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"></script>


</body>

</html>
