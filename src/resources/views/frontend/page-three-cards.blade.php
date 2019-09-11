@extends('laravel-cms::' . $helper->s('template.frontend_dir') .  '.includes.layout')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md pb-5 page-content">

<div class="text-center mb-4 main-title">
    <h1>{{$page->title}}</h1>
</div>

<div class="mb-4 main-content">
    {!! $page->main_content !!}
</div>

<div class="text-center mb-4 main-image">
    @if ( isset($file_data->main_image) )
        <img src="{{$helper->imageUrl($file_data->main_image, '1000') }}" class="img-fluid" />
    @endif
</div>


<div class="row mb-4 img-cards">
    <div class="col-md-4 first-card">
        @include('laravel-cms::' . $helper->s('template.frontend_dir') .  '.includes.image-card', ['extra_id' => 1, 'width'=>'auto', 'height'=>$helper->s('file.middle_image_height') ])
    </div>
    <div class="col-md-4 second-card">
        @include('laravel-cms::' . $helper->s('template.frontend_dir') .  '.includes.image-card', ['extra_id' => 2, 'width'=>'auto', 'height'=>$helper->s('file.middle_image_height') ])
    </div>

    <div class="col-md-4 third-card">
        @include('laravel-cms::' . $helper->s('template.frontend_dir') .  '.includes.image-card', ['extra_id' => 3, 'width'=>'auto', 'height'=>$helper->s('file.middle_image_height') ])
    </div>

</div>

<div class="text-center mb-4 main-text">
    {!! $page->sub_content !!}
</div>

@include('laravel-cms::' . $helper->s('template.frontend_dir') .  '.includes.sub-page-cards', ['sub_pages' => $page->children, 'card_class'=>'col-md-6 mb-4', 'img_width'=>$helper->s('file.small_image_width'), 'img_height'=>$helper->s('file.small_image_height') ])


        </div>
    </div>
</div>

@endsection
