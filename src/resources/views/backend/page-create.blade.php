@extends('laravel-cms::' . config('laravel-cms.template_backend_dir')  .  '.includes.layout')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md">

{{-- {!! Form::open(['route' => ['LaravelCmsAdminPages.store'], 'method' => "POST", 'files'=>true]) !!} --}}

{!! Form::model($_GET, ['route' => ['LaravelCmsAdminPages.store'], 'method' => "POST", 'files'=>true]) !!}

@include('laravel-cms::' . config('laravel-cms.template_backend_dir') .  '.includes.page-form')

<div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i>Create</button>
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
