@extends('laravel-cms::' . config('laravel-cms.template_frontend_dir') .  '.includes.layout')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md pb-5 page-content">

<div class="text-center main-title">
    <h1>{{$page->title}}</h1>
</div>

<div class="main-content">
    {!! $page->main_content !!}
</div>

<div class="text-center main-image">
    @if ( isset($file_data->main_image) )
        <img src="{{$helper->imageUrl($file_data->main_image, '1000') }}" class="img-fluid" />
    @endif
</div>


<div class="row mt-3 cards">
    <div class="col-md-4 first-card">
        @include('laravel-cms::' . config('laravel-cms.template_frontend_dir') .  '.includes.image-card', ['extra_id' => 1, 'width'=>'auto', 'height'=>200 ])
    </div>
    <div class="col-md-4 second-card">
        @include('laravel-cms::' . config('laravel-cms.template_frontend_dir') .  '.includes.image-card', ['extra_id' => 2, 'width'=>'auto', 'height'=>200 ])
    </div>

    <div class="col-md-4 third-card">
        @include('laravel-cms::' . config('laravel-cms.template_frontend_dir') .  '.includes.image-card', ['extra_id' => 3, 'width'=>'auto', 'height'=>200 ])
    </div>

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
