@if ( $sub_pages )
<div class="row mb-4 sub-pages">
    @foreach ($sub_pages as $sub_page)

    <div class="{{$card_class ?? 'col-md-4 mb-4' }}">
        <div class="card">
            <h4 class="text-truncate card-header"><a href="{{ $helper->url($sub_page)}}"
                    title="{{$sub_page->title}}">{{$sub_page->menu_title ?? $sub_page->title}}</a>
            </h4>
            <div class="card-body">
                @php
                $file_data = json_decode($sub_page->file_data);
                @endphp
                @if ( isset($img_width) && isset($file_data->main_image))
                <a href="{{ $helper->url($sub_page) }}" title="{{$sub_page->title}}">
                    <img class="float-left mr-2 img-fluid img-thumbnail p-0 sub-page-img"
                        src="{{$helper->imageUrl($file_data->main_image, $img_width ,( $img_height ?? 'auto') ) }}"
                        alt="{{$sub_page->title}}" />
                </a>
                @endif
                <p class="card-text">{!! $sub_page->abstract ??
                    \Illuminate\Support\Str::limit(strip_tags($sub_page->main_content), 180) !!}</p>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
