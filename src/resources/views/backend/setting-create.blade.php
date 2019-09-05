@extends('laravel-cms::' . $helper->getCmsSetting('template_backend_dir')  .  '.includes.layout')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md">


{!! Form::model($_GET, ['route' => ['LaravelCmsAdminSettings.store'], 'method' => "POST", 'files'=>true]) !!}
@include('laravel-cms::' . $helper->getCmsSetting('template_backend_dir') .  '.includes.setting-form')

<div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i>Create a Setting</button>
</div>
{{ Form::close() }}

        </div>
    </div>
</div>

@endsection
