@extends($helper->bladePath('includes.layout','b'))

@section('content')

<div class="container">
    <div class="row justify-content-center mt-2">
        <div class="col-md" id="react-js-sap">
            <div class="m-5 text-center">
                <i class="fas fa-spinner fa-spin text-primary mr-1"></i>
                Loading ...
            </div>
        </div>
    </div>
</div>

<!-- Load ReactJs scripts start -->
@include($helper->bladePath('includes.react-js-scripts','b'))

<!-- Main ReactJs -->
<script src="{{$helper->assetUrl('js/reactLaravelCmsBackend.js', true, true) }}"></script>

<!-- Load ReactJs scripts end -->
@endsection
