@php
$extra_image = 'extra_image_' . $extra_id;
$extra_text = 'extra_text_' . $extra_id;
$extra_content = 'extra_content_' . $extra_id;
@endphp

<div class="card mb-4">
    @if ( isset($file_data->$extra_image) )
    <div class="extra-image extra-image-{{$extra_id}}">
        <a href="{{ $page->$extra_text }}" title="{{$page->title}}">
            <img class="img-fluid card-img-top" src="{{$helper->imageUrl($file_data->$extra_image, $width ,$height) }}"
                alt="" />
        </a>
    </div>
    @else
    <div class="card-header extra-text extra-text-{{$extra_id}}">
        <h4>
            {!! $page->$extra_text !!}
        </h4>
    </div>
    @endif
    <div class="card-body">
        <div class="card-text extra-content extra-content-{{$extra_id}}">
            {!! $page->$extra_content !!}
        </div>
    </div>

</div>
