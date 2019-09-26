<!doctype html>
<html lang="{{$helper->s('template.frontend_language')}}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        {{$page->meta_title ?? $page->title}}{{strlen($page->meta_title ?? $page->title) < 60 ? ' - ' . $helper->s('site_name') : ''}}
    </title>
    @if ( isset($page->meta_keywords) )
    <meta name="keywords" content="{{$page->meta_keywords}}" />
    @endif
    @if ( isset($page->meta_description) )
    <meta name="description" content="{{$page->meta_description}}" />
    @endif
    <meta name="generator" content="Amila Laravel CMS" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.9.0/css/all.min.css">

    <link rel="icon" href="{{ $helper->s('favicon_url') ?? 'favicon.ico'}}" />

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.4.1/dist/jquery.min.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="{{ $helper->assetUrl('css/main.css') }}">

    @if ( $helper->s('template.full_screen') == '1')
    <style>
        .container {
            max-width: 100%;
        }
    </style>
    @endif
    {{-- for any code that must be put in the <head> --}}
    @if ( $helper->s('page_head') )
    {!! $helper->s('page_head') !!}
    @endif
</head>

<body class="cms-page {{$page->template_file}} slug-{{str_replace('.html', '', $page->slug) }}"
    id="cms-page-{{$page->id}}">

    @include($helper->bladePath('includes.header'))

    @yield('content')

    @include($helper->bladePath('includes.footer'))

    <script src="{{ $helper->assetUrl('js/bottom.js') }}"></script>


    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.15.0/dist/umd/popper.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"></script>

    {{-- for any code may effect the page load speed, eg. analytics,ads --}}
    @if ( $helper->s('page_bottom') )
    <div class="container">
        {!! $helper->s('page_bottom') !!}
    </div>
    @endif

</body>

</html>
