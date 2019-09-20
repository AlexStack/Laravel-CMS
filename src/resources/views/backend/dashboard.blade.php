@extends($helper->bladePath('includes.layout','b'))

@section('content')

<div class="container dashboard">
    <div class="row justify-content-center mt-2">
        {{-- Start main content --}}

        <div class="col-md-auto text-center">

            <h1>{{ $helper->t('dashboard')}}</h1>
            <div class="cms-version-notice">{!! $helper->t('cms_version_notice', [
                'current'=>'<span class="text-success font-weight-bold current_version">' . $cms_version .'</span>',
                'latest'=>'<span class="text-danger font-weight-bold latest_version">***</span>'
                ]) !!}</div>

        </div>

        <div class="w-100 mb-4"></div>

        {{-- latest-settings --}}
        <div class="col-md-4 mb-2  latest latest-settings">

            <ul class="list-group">
                <li class="list-group-item list-group-item-action bg-light header">
                    <a href="{{ route('LaravelCmsAdminSettings.index') }}">
                        <h4>{{$helper->t('latest_name',['name'=>$helper->t('settings')])}}</h4>
                    </a> @foreach( $latest_settings as $item)
                <li class="list-group-item list-group-item-action text-truncate"><a
                        href="{{ route('LaravelCmsAdminSettings.edit', ['setting' => $item->id]) }}"
                        title="{{$helper->t('updated_at') . ':' . $item->updated_at . ', ' . $helper->t('created_at') . ':' . $item->created_at}}">
                        <span class="badge badge-secondary">{{$loop->index+1}}</span>
                        {{$item->category}}.{{$item->param_name}}
                    </a></li>
                @endforeach
            </ul>
        </div>

        {{-- latest-pages --}}
        <div class="col-md-4 mb-2  latest latest-pages">

            <ul class="list-group">
                <li class="list-group-item list-group-item-action bg-light header">
                    <h4>
                        <a href="{{ route('LaravelCmsAdminPages.index') }}">
                            {{$helper->t('latest_name',['name'=>$helper->t('pages')])}}</a>
                        <a href="{{ route('LaravelCmsAdminPages.create', ['menu_enabled'=>0,'switch_nav_tab'=>'settings']) }}"
                            title="{{$helper->t('create_new_page')}}"><i
                                class="far fa-plus-square ml-3 shadow-sm"></i></a>
                    </h4>
                </li>

                @foreach( $latest_pages as $item)
                <li class="list-group-item list-group-item-action text-truncate">
                    <a href="{{ route('LaravelCmsAdminPages.edit', ['page' => $item->id]) }}"
                        title="{{$helper->t('updated_at') . ':' . $item->updated_at . ', ' . $helper->t('created_at') . ':' . $item->created_at}}">
                        <span class="badge badge-secondary">{{$loop->index+1}}</span>
                        @if ( $item->menu_title)
                        {{$item->menu_title}}
                        @else
                        {{$item->title}}
                        @endif
                    </a></li>
                @endforeach
            </ul>
        </div>

        {{-- latest-files --}}
        <div class="col-md-4 mb-2  latest latest-files">

            <ul class="list-group">
                <li class="list-group-item list-group-item-action bg-light header">
                    <a href="{{ route('LaravelCmsAdminFiles.index') }}">
                        <h4>{{$helper->t('latest_name',['name'=>$helper->t('files')])}}</h4>
                    </a></li>

                @forelse( $latest_files as $item)
                <li class="list-group-item list-group-item-action text-truncate"><a
                        href="{{ $helper->imageUrl($item) }}" target="_blank"
                        title="{{$helper->t('updated_at') . ':' . $item->updated_at . ', ' . $helper->t('created_at') . ':' . $item->created_at}}">
                        <span class="badge badge-secondary">{{$loop->index+1}}</span>
                        {{$item->title}}
                    </a></li>
                @empty
                <li class="list-group-item list-group-item-action text-truncate">
                    <a href="{{ route('LaravelCmsAdminFiles.index') }}">
                        <i class="fas fa-upload mr-1"></i>{{$helper->t('upload,file')}}
                    </a>
                </li>
                @endforelse

                @if ( $latest_files->count() < 10 ) <li class="list-group-item list-group-item-action text-truncate">
                    <a href="{{ route('LaravelCmsAdminFiles.index') }}">
                        <i class="fas fa-upload mr-1"></i>{{$helper->t('upload,file')}}
                    </a>
                    </li>
                    @endif
            </ul>
        </div>

        {{-- software versions --}}

        <div class="m-3 text-secondary text-truncate software-version">
            <i class="fab fa-laravel text-warning font-weight-bold"></i> Laravel {{$helper->t('version')}}
            {{app()->version()}}
            <i class="fab fa-php text-primary ml-4"></i> PHP {{$helper->t('version')}} {{ phpversion() }}

            @if ( strpos(PHP_OS, 'WIN') !== false )
            <i class="fab fa-windows text-info ml-4"></i>
            @else
            <i class="fab fa-linux text-info ml-4"></i>
            @endif
            {{$helper->t('server_os')}}
            {{ strpos(ini_get('disable_functions'),'php_uname') === false ? php_uname('s') . ' ' . php_uname('r') : PHP_OS }}
        </div>

        {{-- End main content --}}
    </div>
</div>


<script>
    // todo: https://api.github.com/repos/alexstack/laravel-cms/releases/latest
    var cmsGitHubTags = $.getJSON( "https://api.github.com/repos/AlexStack/Laravel-CMS/tags", function(data) {
        console.log( "success" );
        console.log(data[0]['name']);
    })
    .done(function(data) {
        console.log( "second success");
        console.log(data[0]['name']);
        $('span.latest_version').html('' + data[0]['name']);
    })
    .fail(function() {
        console.log( "error" );
    })
    .always(function() {
        console.log( "complete" );
    });
</script>

@endsection
