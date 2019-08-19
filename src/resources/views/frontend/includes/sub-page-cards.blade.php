@if ( $sub_pages )
<div class="row sub-pages">
@foreach ($sub_pages as $sub_page)

<div class="{{$card_class ?? 'col-md-4 mb-4' }}">
    <div class="card">
    <h4 class="card-header"><a href="{{ route('LaravelCmsPages.show', ($sub_page->slug ?? $sub_page->id . '.html'), false ) }}">{{$sub_page->menu_title ?? $sub_page->title}}</a></h4>
      <div class="card-body">
        @php
            $file_data = json_decode($sub_page->file_data);
        @endphp
        @if ( isset($img_width) && isset($file_data->main_image))
            <a href="{{ route('LaravelCmsPages.show', ($sub_page->slug ?? $sub_page->id . '.html'), false ) }}" title="{{$sub_page->title}}">
            <img class="float-left mr-2 img-fluid img-thumbnail p-0 sub-page-img" src="{{$helper->imageUrl($file_data->main_image, $img_width ,( $img_height ?? 'auto') ) }}" alt="{{$sub_page->title}}" />
            </a>
        @endif
        <p class="card-text">{!! $sub_page->abstract ?? str_limit(strip_tags($sub_page->main_content), 180) !!}</p>
      </div>
    </div>
  </div>
@endforeach
</div>
@endif
