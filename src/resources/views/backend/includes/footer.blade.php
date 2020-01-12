<div class="container">
    <div class="row justify-content-center">
        <div class="col-md text-center mt-5 mb-5 footer">

            <span class="small">
                <a href="https://github.com/AlexStack/Laravel-CMS" target="_blank" class="text-dark">Laravel CMS</a> -
                <a href="https://github.com/AlexStack/Laravel-CMS/issues" target="_blank" class="text-dark">Bug
                    Report</a> -
                {{date('Y')}}-{{date('Y')+1}}
            </span>

            <div class="row justify-content-center">
                <span class="small text-secondary mt-3 admin-user"
                    title="{{$helper->t($helper->user->laravel_cms_admin_role)}}">
                    {{ucfirst($helper->user->name)}}
                </span>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/popper.js@1.15.0/dist/umd/popper.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.10.0-rc3/Sortable.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/jquery-sortablejs@latest/jquery-sortable.js"></script>

<script src="{{$helper->assetUrl('js/summernote-ext-highlighter.js', true, true) }}"></script>

<script src="{{$helper->assetUrl('js/bottom.js', true, true) }}"></script>

<script>
    $(function() {
            switchNavTab("{{ $_GET['switch_nav_tab'] ?? '' }}");
            sortableList('#sortableList');
        });
</script>

{!! $helper->loadPluginJs('js_for_all_admin_pages') !!}

@if (request()->route()->getName() == 'LaravelCmsAdmin.index' && $helper->s('system.all_pages.react_js') &&
isset($_COOKIE['laravel_cms_access_num']) &&
$_COOKIE['laravel_cms_access_num'] <= 2) <!-- Pre-Load(browser cache) ReactJS extra scripts so the All Pages list will
    load faster -->
    @include($helper->bladePath('includes.react-js-scripts','b'))

    @endif
