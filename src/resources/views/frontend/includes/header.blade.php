<header class="container ">
    {!! config('laravel-cms.page_top') !!}
    <nav class="navbar navbar-expand-lg navbar-light top-nav">
        <a class="navbar-brand" href="{{ route('LaravelCmsPages.index', [], false) }}" title="Home">
        {{-- <% if $SiteConfig.TopLogo %>
            <img src="$SiteConfig.TopLogo.URL" class="top-logo" />
        <% else %> --}}
            <img src="{{ config('laravel-cms.top_logo') ?? 'https://via.placeholder.com/250x70/ebf0f5/000000/?text=Top+Logo' }}" class="top-logo" />
        {{-- <% end_if %> --}}
        </a>
        <button
            class="navbar-toggler"
            type="button"
            data-toggle="collapse"
            data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto ">
                @foreach ( $menus as $item)
                <li class="nav-item
                    @if ( count($item->menus) >0 )
                    dropdown
                    @endif
                 {{-- <% if $isCurrent %>active<% end_if %> --}}" >
                    @if ( count($item->menus) >0 )
                    <a class="nav-link dropdown-toggle" href="{{ $helper->url($item)  }}" id="drop{{$item->id}}" role="button" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown" >{{ $item->menu_title ?? $item->title }}
                        <span class="icon d-none d-lg-inline icon-down-arrow"></span>
                        <span class="icon opener d-lg-none icon-down-arrow"></span>
                        <span class="sr-only">(current)</span></a>
                        @if ( count($item->menus) >0 )
                            <div class="dropdown-menu" aria-labelledby="drop{{$item->id}}">
                            @foreach ( $item->menus as $item2)
                                <a class="dropdown-item" href="{{  $helper->url($item2)  }}">
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
{{--
                <% if $CurrentMember %>
                <li class="nav-item dropdown member">
                    <a
                        class="nav-link dropdown-toggle"
                        href="#"
                        id="navbarDropdown"
                        role="button"
                        data-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false"
                    >
                        Hi, $CurrentMember.FirstName
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a
                            class="dropdown-item"
                            href="/Security/logout?SecurityID=$SecurityID&BackURL=/"
                            ><i class="fas fa-lock  mr-2"></i>Logout
                        </a>

                        <% if $CurrentMember.inGroup(2) %>
                        <a class="dropdown-item" href="/admin"
                            ><i class="fas fa-tools mr-2"></i>Admin Panel</a
                        >
                        <% end_if %>
                    </div>
                </li>
                <% else %>
                <li class="nav-item member">
                    <a href="/Security/login?BackURL=%2F" class="nav-link"
                        >User Login</a
                    >
                </li>
                <% end_if %> --}}
            </ul>
        </div>
    </nav>
    @if ( isset($file_data->main_banner) )
        <div class="text-center top-banner" style="min-height:100px; background-image: url({{$helper->imageUrl($file_data->main_banner, '2000') }});" ></div>
    @endif
</header>
