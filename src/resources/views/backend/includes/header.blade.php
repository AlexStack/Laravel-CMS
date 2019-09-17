<header class="container top-header">
    <div class="row justify-content-start">
        <div class="col-auto menu-logo">
            <h2>
                <a href="{{ route('LaravelCmsAdmin.index') }}" title="{{ $helper->t('dashboard')}}"><img
                        class="img-fluid top-logo" src="{{$helper->assetUrl('images/top-logo.png', false, true) }}"
                        style="height:80px;" alt="Laravel CMS" /></a>
            </h2>
        </div>
        <div class="col text-right pt-3 menu-links">
            @php
            $menu_links = $helper->s('category.admin_menu_links');
            if ( is_array($menu_links)){
            foreach($menu_links as $link){
            if ( is_array($link)) {
            if ( $link['style'] == 'dropdown'){
            echo '<div class="btn-group">
                ' . $link['button'] . '
                <div class="dropdown-menu">';
                    foreach( $link['items'] as $item ){
                    echo $item;
                    }
                    echo '</div>
            </div>';
            }
            }
            else {
            echo $link;
            }
            }
            }
            @endphp



            @if ( !is_array($menu_links) )
            <a class="btn btn-success mr-3" href="{{ route('LaravelCmsAdminPages.index') }}" role="button"><i
                    class="fas fa-home mr-1"></i>{{ $helper->t('all_page') }}</a>

            <a class="btn btn-primary mr-3" href="{{ route('LaravelCmsAdminPages.create') }}" role="button"><i
                    class="fas fa-plus-circle mr-1"></i>{{ $helper->t('create_new_page') }}</a>

            <a class="btn btn-secondary" href="{{ route('LaravelCmsAdminSettings.index') }}" role="button"><i
                    class="fas fa-cog mr-1"></i>CMS {{ $helper->t('settings') }}</a>
            @endif

        </div>
    </div>
</header>
