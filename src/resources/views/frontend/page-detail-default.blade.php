@extends('laravel-cms::' . $helper->s('template_frontend_dir') .  '.includes.layout')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md pb-5 page-content">

<div class="text-center main-title">
    <h1>{{$page->title}}</h1>
</div>


<div class="mb-4 main-content">
    {!! $page->main_content !!}
</div>

<div class="text-center mb-4 main-image">
    @if ( isset($file_data->main_image) )
        <img src="{{$helper->imageUrl($file_data->main_image, $helper->s('image.big_image_width')) }}" class="img-fluid" />
    @endif
</div>


<div class="text-center mb-4 sub-content">
    {!! $page->sub_content !!}
</div>

{{-- Extra_text_1 external_link format: http://link_url | link_text --}}
@php
if ( strpos($page->extra_text_1, 'http') !== false && strpos($page->extra_text_1, '|') !== false ){
    $external_link_ary = explode('|', $page->extra_text_1);
    echo '<div class="text-center mb-4 external-link">
        <a href="' . route('LaravelCmsPages.show','redirect-link',false) . '?url=' . urlencode(trim($external_link_ary[0])) . '" class="btn btn-primary" target="_blank" rel="nofollow" >' . trim($external_link_ary[1]) . '</a>
    </div>';
}
@endphp

@include('laravel-cms::' . $helper->s('template_frontend_dir') .  '.includes.sub-page-cards', ['sub_pages' => $page->children, 'card_class'=>'col-md-4 mb-4', 'img_width'=>$helper->s('image.small_image_width'), 'img_height'=>$helper->s('image.big_image_height')  ])


@include('laravel-cms::' . $helper->s('template_frontend_dir') .  '.includes.breadcrumb')


{!! isset($plugins['page-tab-inquiry-form']) ?? $plugins['page-tab-inquiry-form']->displayForm($page) !!}

        </div>
    </div>
</div>

@endsection
