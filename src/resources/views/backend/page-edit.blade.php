@extends('laravel-cms::' . config('laravel-cms.template_backend_dir')  .  '.includes.layout')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md">

{!! Form::model($page_model, ['route' => ['LaravelCmsAdminPages.update', $page_model->id], 'method' => "PUT", 'files'=>true]) !!}

    @include('laravel-cms::' . config('laravel-cms.template_backend_dir') .  '.includes.page-form')

    <div class="row">
      <div class="col-md">
          <button type="submit" class="btn btn-primary"><i class="far fa-save mr-2"></i>Save</button>
      </div>
      <div class="col-md text-right">
          <button type="button" class="btn btn-danger" onclick="return confirmDelete(form);"><i class="fas fa-trash-alt mr-2" ></i>Delete</button>
      </div>
    </div>
      {{ Form::close() }}
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('textarea').summernote({
        placeholder: '',
        tabsize: 2,
        minHeight: 120,
        maxHeight: 600
      });
    });
    function confirmDelete(f){
        var del_msg = "Confirm to delete?";
        if ( confirm(del_msg) ) {
            f._method.value  = 'DELETE';
            f.action = "{{route('LaravelCmsAdminPages.destroy', $page_model->id)}}";
            f.submit();
        }
    }
</script>

@endsection
