<!doctype html>
<html lang="{{config('laravel-cms.template_language')}}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{$page->meta_title ?? $page->title}}</title>
        @if ( $page->meta_keywords )
            <meta name="keywords" content="{{$page->meta_keywords}}" />
        @endif
        @if ( $page->meta_description )
            <meta name="description" content="{{$page->meta_description}}" />
        @endif

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css">

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.9.0/css/all.min.css">

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" crossorigin="anonymous"></script>

        <link rel="stylesheet" href="{{ $helper->assetUrl('css/main.css') }}">

    </head>
    <body class="{{$page->template_file}} page-{{$page->id}} slug-{{str_replace('.html', '', $page->slug) }}">
        @include('laravel-cms::' . config('laravel-cms.template_frontend_dir') .  '.includes.header')

        @yield('content')

        @include('laravel-cms::' . config('laravel-cms.template_frontend_dir') .  '.includes.footer')

        <script src="{{ $helper->assetUrl('js/bottom.js') }}"></script>


        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    </body>
</html>
