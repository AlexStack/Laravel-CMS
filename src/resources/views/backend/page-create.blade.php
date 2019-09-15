@extends('laravel-cms::' . $helper->s('template.backend_dir') . '.includes.layout')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md">

            {{-- {!! Form::open(['route' => ['LaravelCmsAdminPages.store'], 'method' => "POST", 'files'=>true]) !!} --}}

            {!! Form::model($_GET, ['route' => ['LaravelCmsAdminPages.store'], 'method' => "POST", 'files'=>true,
            'id'=>'page_content_form']) !!}

            @include('laravel-cms::' . $helper->s('template.backend_dir') . '.includes.page-form')

            @include('laravel-cms::' . $helper->s('template.backend_dir') . '.includes.submit-button')

            {{ Form::close() }}

        </div>
    </div>
</div>
@endsection
