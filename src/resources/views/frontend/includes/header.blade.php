<header class="container ">
    {!! $helper->s('page_top') !!}
    <nav class="navbar navbar-expand-lg navbar-light top-nav">
        <a class="navbar-brand" href="{{ route('LaravelCmsPages.index', [], false) }}"
            title="{{$helper->s('site_name')}}">
            <img src="{{ $helper->s('top_logo') ?? 'https://via.placeholder.com/250x70/ebf0f5/000000/?text=Top+Logo' }}"
                class="top-logo" />
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto ">
                @foreach ( $menus as $item)
                <li class="nav-item
                    @if ( count($item->menus) >0 )
                        dropdown
                    @endif
                    {{$helper->activeMenuClass($item, $page, ' active')}}">
                    @if ( count($item->menus) >0 )
                    <a class="nav-link dropdown-toggle" href="{{ $helper->url($item)  }}" id="drop{{$item->id}}"
                        role="button" aria-haspopup="true" aria-expanded="false"
                        data-toggle="dropdown">{{ $item->menu_title ?? $item->title }}
                        <span class="icon d-none d-lg-inline icon-down-arrow"></span>
                        <span class="icon opener d-lg-none icon-down-arrow"></span>
                        <span class="sr-only">(current)</span></a>
                    @if ( count($item->menus) >0 )
                    <div class="dropdown-menu" aria-labelledby="drop{{$item->id}}">
                        @foreach ( $item->menus as $item2)
                        <a class="dropdown-item {{$helper->activeMenuClass($item2, $page, 'active')}}"
                            href="{{  $helper->url($item2)  }}">
                            {{ $item2->menu_title ?? $item2->title }}
                        </a>
                        @endforeach
                    </div>
                    @endif
                    @else
                    <a class="nav-link" href="{{ $helper->url($item)  }}">
                        {{ $item->menu_title ?? $item->title }}
                    </a>
                    @endif
                </li>
                @endforeach

            </ul>
        </div>
    </nav>
    @if ( isset($file_data->main_banner) )
    <div class="text-center top-banner"
        style="min-height:100px; background-image: url({{$helper->imageUrl($file_data->main_banner, '2000') }});"></div>
    @endif
</header>
