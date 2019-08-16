@extends('laravel-cms::laravel-cms-frontend.frontend-layout')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md">
<div class="text-center main_title"><h1>{{$page->title}}</h1></div>

<div class="main_content">

    @if ( isset($file_data->main_image) )
        <img src="{{$controller->imageUrl($file_data->main_image, '800') }}" class="img-fluid" />

        <img src="{{$controller->imageUrl($file_data->main_image, '500') }}" class="img-fluid" />

        <img src="{{$controller->imageUrl($file_data->main_image, 'w', '150') }}" class="img-fluid" />

        <img src="{{$controller->imageUrl($file_data->main_image, '100', '100') }}" class="img-fluid" />
    @endif

    {!! $page->main_content !!}
</div>

@if ( $page->children )
<div class="row sub-pages">
@foreach ($page->children as $sub_page)


  <div class="col-md-4">
    <div class="card">
    <h4 class="card-header"><a href="{{ route('LaravelCmsPages.show', ($sub_page->slug ?? $sub_page->id . '.html') ) }}">{{$sub_page->menu_title ?? $sub_page->title}}</a></h4>
      <div class="card-body">
        <p class="card-text">{!! $sub_page->abstract ?? str_limit(strip_tags($sub_page->main_content), 180) !!}</p>
      </div>
    </div>
  </div>

@endforeach
</div>
@endif
        </div>
    </div>
</div>

@endsection
