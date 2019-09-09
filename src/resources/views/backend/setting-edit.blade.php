@extends('laravel-cms::' . $helper->s('template.backend_dir')  .  '.includes.layout')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md">

{!! Form::model($setting, ['route' => ['LaravelCmsAdminSettings.update', $setting->id], 'method' => "PUT", 'files'=>true]) !!}

    @include('laravel-cms::' . $helper->s('template.backend_dir') .  '.includes.setting-form')

    <div class="row">
      <div class="col-md">
          @include('laravel-cms::' . $helper->s('template.backend_dir') .  '.includes.submit-button')
      </div>
      <div class="col-md-auto text-right">
          <button type="button" class="btn btn-danger" onclick="return confirmDelete(form);"><i class="fas fa-trash-alt mr-2" ></i>{{$helper->t('delete')}}</button>
      </div>
    </div>
      {{ Form::close() }}
        </div>
    </div>
</div>
<script>
    function confirmDelete(f){
        var del_msg = "Confirm to delete?";
        if ( confirm(del_msg) ) {
            f._method.value  = 'DELETE';
            f.action = "{{route('LaravelCmsAdminSettings.destroy', $setting->id)}}";
            f.submit();
        }
    }
</script>

@endsection
