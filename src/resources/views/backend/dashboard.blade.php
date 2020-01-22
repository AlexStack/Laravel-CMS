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
                    <h4>
                        <a href="{{ route('LaravelCmsAdminSettings.index') }}">
                            {{$helper->t('latest_name',['name'=>$helper->t('settings')])}}</a>

                        <a href='{{route('LaravelCmsAdminSettings.index','category=plugin&search_plugin=yes')}}'
                            title="{{$helper->t('install,plugins')}}">
                            <i class='fas fa-cogs ml-3 small'></i></a>
                    </h4>
                    @foreach( $latest_settings as $item)
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
                            title="{{$helper->t('create_new_page')}}"><i class="far fa-plus-square ml-3 small"></i></a>
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
                    </a>
                    <a class="text-secondary float-right ml-2 " href="{{$helper->url($item)}}" target="_blank"><i
                            class="fas fa-external-link-square-alt small"></i></a>
                </li>
                @endforeach
            </ul>
        </div>

        {{-- latest-files --}}
        <div class="col-md-4 mb-2  latest latest-files">

            <ul class="list-group">
                <li class="list-group-item list-group-item-action bg-light header">
                    <h4>
                        <a href="{{ route('LaravelCmsAdminFiles.index') }}">
                            {{$helper->t('latest_name',['name'=>$helper->t('files')])}}</a>

                        <a href='{{route('LaravelCmsAdminSettings.index','category=template&search_template=yes')}}'
                            title="{{$helper->t('template')}}">
                            <i class="fas fa-globe ml-3 small"></i></a>

                    </h4>
                </li>

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
                        <i class="fas fa-upload badge-secondary mr-1"></i>{{$helper->t('upload,file')}}
                    </a>
                </li>
                <li class="list-group-item list-group-item-action text-truncate">
                    <a href="{{ route('LaravelCmsAdminFiles.index') }}">
                        <i class="fas fa-upload badge-secondary mr-1"></i>{{$helper->t('upload,file')}}
                    </a>
                </li>
                @endforelse

                @if ( $latest_files->count() < 10 ) <li class="list-group-item list-group-item-action text-truncate">
                    <a href="{{ route('LaravelCmsAdminFiles.index') }}">
                        <i class="fas fa-upload badge-secondary mr-1"></i>{{$helper->t('upload,file')}}
                    </a>
                    </li>
                    @endif
            </ul>
        </div>

        {{-- software versions --}}

        <div class="m-3 text-secondary text-truncate software-version">
            <i class="fab fa-laravel text-warning font-weight-bold"></i> Laravel {{$helper->t('version')}}
            {{app()->version()}}
            <i class="fab fa-php text-primary ml-4"></i> <a href="?show_phpinfo=yes" target="_blank"
                title="phpinfo()">PHP
                {{$helper->t('version')}} {{ phpversion() }}</a>

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
    var cmsGitHubTags = $.getJSON( "https://api.github.com/repos/alexstack/laravel-cms/releases/latest", function(data) {
        // console.log( "success" );
        // console.log(data['tag_name']);
    })
    .done(function(data) {
        // console.log( "second success");
        // console.log(data['tag_name']);
        var latest_version = data['tag_name'];
        if ( '{{$cms_version}}' !== data['tag_name'] ){
            latest_version = '<a href="./files?new_version=' + data['tag_name'] + '&old_version={{$cms_version}}" target="_blank" class="text-danger update-cms" onclick="return updateCms();">' + data['tag_name'] + '</a>';
        }
        $('span.latest_version').html(latest_version);
    })
    .fail(function() {
        console.log( "cmsGitHubTags error" );
    })
    .always(function() {
        console.log( "cmsGitHubTags complete" );
    });
</script>

<script>
    // major information from the official website
    // only display for super_admin, NOT for web_admin & content_admin
    var cms_access_num = {{$_COOKIE['laravel_cms_access_num'] ?? 0}};
    if ( admin_role == 'super_admin'){
        var majorInfo = $.getJSON( "https://www.laravelcms.tech/Laravel-Major-information-for-the-dashboard.html?response_type=json", function(data) {
            console.log( "get majorInfo success" );
            //console.log(data);
        })
        .done(function(data) {
            console.log( "get majorInfo second success");
            var infoJson   = JSON.parse(data['page']['special_text']);
            var infoHtml   = '';
            var localeData = infoJson['en'];
            //console.log(infoJson);
            if('{{$helper->s("template.backend_language")}}' in infoJson){
                localeData = infoJson['{{$helper->s("template.backend_language")}}'];
            }
            if ( typeof(localeData) == 'object' && 0 in localeData){
                for (var num in localeData) {
                   if ( num <= cms_access_num ){
                        infoHtml = localeData[num];
                   }
                }
            } else {
                infoHtml = localeData;
            }
            $('span.latest_version').after('<div class="text-secondary major-info">'+ infoHtml + '</div>');
            $('.major-info').fadeIn('slow');
        })
        .fail(function() {
            console.log( "get majorInfo error" );
        })
        .always(function() {
            //console.log( "get majorInfo complete" );
        });
    }

    function updateCms()    {
        return confirm("Are you sure to update the CMS online via browser? It may take few minutes, please keep the browser open until it complete.");
    }
</script>

@endsection
