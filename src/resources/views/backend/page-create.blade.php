@extends('laravel-cms::' . $helper->getCmsSetting('template_backend_dir')  .  '.includes.layout')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md">

{{-- {!! Form::open(['route' => ['LaravelCmsAdminPages.store'], 'method' => "POST", 'files'=>true]) !!} --}}

{!! Form::model($_GET, ['route' => ['LaravelCmsAdminPages.store'], 'method' => "POST", 'files'=>true]) !!}

@include('laravel-cms::' . $helper->getCmsSetting('template_backend_dir') .  '.includes.page-form')

<div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i>Create</button>
</div>
{{ Form::close() }}

        </div>
    </div>
</div>
{{-- <script>
    function renderEditor(id, minHeight=120) {
        $(id).summernote({
            placeholder: '',
            tabsize: 2,
            minHeight: minHeight,
            maxHeight: 600
        });
    }
    $(document).ready(function() {
        renderEditor('#main_content',200);
        setTimeout(function(){
            renderEditor('#sub_content');
        }, 1500);
        setTimeout(function(){
            renderEditor('#abstract');
            renderEditor('#extra_content_1');
            renderEditor('#extra_content_2');
            renderEditor('#extra_content_3');
        }, 3000);
    });
    </script> --}}

@endsection
