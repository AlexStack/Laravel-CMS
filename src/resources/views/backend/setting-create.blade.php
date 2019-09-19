@extends('laravel-cms::' . $helper->s('template.backend_dir') . '.includes.layout')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md">

            {!! Form::model($_GET, ['route' => ['LaravelCmsAdminSettings.store'], 'method' => "POST", 'files'=>true])
            !!}
            @include('laravel-cms::' . $helper->s('template.backend_dir') . '.includes.setting-form')

            @include('laravel-cms::' . $helper->s('template.backend_dir') . '.includes.submit-button')


            {{ Form::close() }}

        </div>
    </div>
</div>

@endsection
