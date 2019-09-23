@extends($helper->bladePath('includes.layout','b'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md">

            {!! Form::model($_GET, ['route' => ['LaravelCmsAdminSettings.store'], 'method' => "POST",
            'id'=>'cms_setting_form'])
            !!}
            @include($helper->bladePath('includes.setting-form','b'))

            @include($helper->bladePath('includes.submit-button','b'))


            {{ Form::close() }}

        </div>
    </div>
</div>

@endsection
