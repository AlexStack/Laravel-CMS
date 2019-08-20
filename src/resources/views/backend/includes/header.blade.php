<div class="container">
    <div class="row justify-content-start">
        <div class="col-md-6">
        <h2>
            <img class="img-fluid top-logo" src="{{$helper->assetUrl('images/top-logo.png', false, true) }}" alt="Laravel CMS" />
        </h2>
        </div>
        <div class="col-md-6 align-middle text-right pt-3">
        <a name="" id="" class="btn btn-success mr-3" href="{{ route('LaravelCmsAdminPages.index') }}" role="button"><i class="fas fa-home mr-1"></i>All Pages</a>

            <a name="" id="" class="btn btn-primary" href="{{ route('LaravelCmsAdminPages.create') }}" role="button"><i class="fas fa-plus-circle mr-1"></i>Create a New Page</a>
        </div>
    </div>
</div>
