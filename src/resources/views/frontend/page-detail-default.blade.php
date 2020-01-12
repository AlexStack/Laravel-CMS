@extends($helper->bladePath('includes.layout'))

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


            <div class="text-center mb-4 sub-content">
                {!! $page->sub_content !!}
            </div>

            {{-- Extra_text_1 external_link format: http://link_url | link_text --}}
            @php
            if ( strpos($page->extra_text_1, 'http') !== false && strpos($page->extra_text_1, '|') !== false ){
            $external_link_ary = explode('|', $page->extra_text_1);
            echo '<div class="text-center mb-4 external-link">
                <a href="' . route('LaravelCmsPages.show',$helper->s('system.reserved_slugs.redirect'),false) . '?url=' . urlencode(trim($external_link_ary[0])) . '"
                    class="btn btn-primary" target="_blank" rel="nofollow">' . trim($external_link_ary[1]) . '</a>
            </div>';
            }
            @endphp

            @include($helper->bladePath('includes.sub-page-cards'), ['sub_pages' =>
            $page->children, 'card_class'=>'col-md-4 mb-4', 'img_width'=>$helper->s('file.small_image_width'),
            'img_height'=>$helper->s('file.small_image_height') ])

            @if ( $page->extra_content_1 && trim(strip_tags($page->extra_content_1,'<img>'))!='')
            <div class="text-center mb-4 extra_content extra_content_1">{!! $page->extra_content_1 !!}</div>
            @endif
            @if ( $page->extra_content_2 && trim(strip_tags($page->extra_content_2,'<img>'))!='')
            <div class="text-center mb-4 extra_content extra_content_2">{!! $page->extra_content_2 !!}</div>
            @endif
            @if ( $page->extra_content_3 && trim(strip_tags($page->extra_content_3,'<img>'))!='')
            <div class="text-center mb-4 extra_content extra_content_3">{!! $page->extra_content_3 !!}</div>
            @endif

            @include($helper->bladePath('includes.tags'))

            @include($helper->bladePath('includes.breadcrumb'))

            {!! isset($plugins['page-tab-inquiry-form']) ? $plugins['page-tab-inquiry-form']->displayForm($page) : '
            <!-- Inquiry plugin disabled -->'
            !!}

        </div>
    </div>
</div>

@endsection
