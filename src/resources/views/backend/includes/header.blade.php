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

            {!! $helper->getAdminMenu() !!}

            @if ( !is_array($helper->s('system.admin_menu_links')) )
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
