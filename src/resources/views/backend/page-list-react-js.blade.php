@extends($helper->bladePath('includes.layout','b'))

@section('content')

<div class="container">
    <div class="row justify-content-center mt-2">
        <div class="col-md" id="react-js-sap">
            <div class="m-5 text-center">
                <i class="fas fa-spinner fa-spin text-primary mr-1"></i>
                {{$helper->t('loading')}} ...
            </div>
        </div>
    </div>
</div>


<script>
    // Simple multi-language for ReactJs
    var cmsLang = new Object();
    cmsLang.keyword = "{{$helper->t('keyword')}}";
    cmsLang.create_new_page = "{{$helper->t('create_new_page')}}";
    cmsLang.all_page = "{{$helper->t('all_page')}}";
    cmsLang.menu_enabled = "{{$helper->t('display_in_menu')}}";
    cmsLang.recently_added = "{{$helper->t('recently_added')}}";
    cmsLang.confirm_delete = "{{$helper->t('confirm,delete')}}";
    // ReactJs settings from CMS
    var recently_added_numbers = {{$helper->s('system.all_pages.recently_added_numbers') ?? 50}};
    var display_limit_numbers  = {{$helper->s('system.all_pages.display_limit_numbers') ?? 1000}};
    var display_option_numbers = {{$helper->s('system.all_pages.display_option_numbers') ?? 20}};
</script>

<!-- Load ReactJs scripts start -->
@include($helper->bladePath('includes.react-js-scripts','b'))

<!-- Main ReactJs -->
<script src="{{$helper->assetUrl('js/reactLaravelCmsBackend.js', true, true) }}"></script>

<!-- Load ReactJs scripts end -->
@endsection
