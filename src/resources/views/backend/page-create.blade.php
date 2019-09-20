@extends($helper->bladePath('includes.layout','b'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md">

            {{-- {!! Form::open(['route' => ['LaravelCmsAdminPages.store'], 'method' => "POST", 'files'=>true]) !!} --}}

            {!! Form::model($_GET, ['route' => ['LaravelCmsAdminPages.store'], 'method' => "POST", 'files'=>true,
            'id'=>'page_content_form']) !!}

            @include($helper->bladePath('includes.page-form','b'))

            @include($helper->bladePath('includes.submit-button','b'))

            {{ Form::close() }}

        </div>
    </div>
</div>
@endsection
