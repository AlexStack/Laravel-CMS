@extends('laravel-cms::backend.backend-layout')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md">

{!! Form::model($page_model, ['route' => ['LaravelCmsAdminPages.update', $page_model->id], 'method' => "PUT", 'files'=>true]) !!}

    @include('laravel-cms::backend.page-form')

      <div>
          <button type="submit" class="btn btn-primary"><i class="far fa-save mr-2"></i>Save</button>
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
    </script>

@endsection
