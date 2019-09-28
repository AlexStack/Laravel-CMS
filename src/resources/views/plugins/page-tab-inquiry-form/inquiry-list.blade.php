@extends($helper->bladePath('includes.layout','b'))

@section('content')


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md">

            <div class="row justify-content-center header-forms">

                {{-- search form --}}
                <div class="col-md-auto">
                    {!! Form::model($_GET, ['route' => ['LaravelCmsPluginInquiry.index'], 'method' => "GET",
                    'id'=>'search_form'])
                    !!}

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i></span>
                        </div>
                        <input type="text" class="form-control keyword" placeholder="Keyword" aria-label="Keyword"
                            value="{{$_REQUEST['keyword'] ?? ''}}" aria-describedby="basic-addon2" name="keyword"
                            id="keyword" />

                        <div class="input-group-append">
                            <button class="btn btn-secondary" type="submit" title="{{$helper->t('search')}}"
                                id="inputGroupinquiryAddon05">{{$helper->t('search')}}</button>
                        </div>
                        @if ( isset($_REQUEST['keyword']) && trim($_REQUEST['keyword']) != '')
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit" title="Show all inquiries belong to this page"
                                onclick="form.keyword.value='';">{{$helper->t('all')}}</button>
                        </div>
                        @elseif ( isset($_REQUEST['page_id']) && trim($_REQUEST['page_id']) != '' )
                        <div class="input-group-append">
                            <button class="btn btn-info" type="submit" title="Show all inquires of all pages"
                                onclick="form.keyword.value='';form.page_id.value='';">{{$helper->t('all')}}</button>
                        </div>
                        @endif
                    </div>
                    <input name="page_id" class="page_id" type="hidden" value="{{ $_REQUEST['page_id'] ?? ''}}" />
                    {{ Form::close() }}
                </div>
                {{-- clear search terms --}}
                <div class="col-md-auto pt-2">
                    @if ( isset($_REQUEST['keyword']) && trim($_REQUEST['keyword']) != '')
                    <span class="">
                        <a href="#"
                            onclick="$('#search_form .keyword').val('');$('#search_form').submit();return false;"><i
                                class="far fa-times-circle text-primary mr-1"></i>{{$_REQUEST['keyword']}}</a>
                    </span>
                    @endif
                    @if ( isset($_REQUEST['page_id']) && trim($_REQUEST['page_id']) != '')
                    <span class="ml-2">
                        <a href="#"
                            onclick="$('#search_form .page_id').val('');$('#search_form').submit();return false;"><i
                                class="far fa-times-circle text-info mr-1"></i>PageID {{$_REQUEST['page_id']}}</a>
                    </span>
                    @endif
                </div>
            </div>

            {{-- show inquiries --}}
            <div class="row justify-content-center inquiries">
                @forelse ($inquiries as $item)
                <div class="col-xl-10 col-lg-11 col-md-12 mb-4 inquiry" id="inquiry-{{$item->id}}">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col text-truncate">
                                    <i class="text-info fas fa-user-edit"></i> <a
                                        href="{{ route('LaravelCmsAdminPages.edit', ['page' => $item->page_id]) }}">{{$item->page_title}}</a>
                                    <span class="text-secondary">({{$item->created_at}})</span>
                                </div>

                                <div class="col-auto">
                                    <a href="#del-{{$item->id}}" class="delete-link" data-id="{{$item->id}}"><i
                                            class="far fa-trash-alt"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="text-secondary card-title">
                                {{$helper->t('name')}}: {{$item->first_name}} {{$item->last_name}}
                                @if ( $item->company_name )
                                - {{$helper->t('company_name')}}: {{$item->company_name}}
                                @endif
                                @if ( $item->email )
                                - {{$helper->t('email')}}: {{$item->email}}
                                @endif
                                @if ( $item->phone )
                                - {{$helper->t('phone')}}: {{$item->phone}}
                                @endif
                            </div>
                            <div class="card-text">{!! nl2br($item->message) !!}</div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="m-5"><i
                        class="text-warning shadow-sm mr-2 fas fa-exclamation-triangle"></i>{{$helper->t('no_result')}}
                </div>
                @endforelse
            </div>

            <div class="row justify-content-center upload-form">
                <div class="col-md-auto  justify-content-center pagination">
                    {{ $inquiries->appends(['keyword' =>$_REQUEST['keyword']??null, 'page_id' =>$_REQUEST['page_id']??null])->links() }}
                </div>
                <div class="w-100"></div>
                <div class="col-md-auto  text-center">
                    <div class="total">{{ $helper->t('total') }} <span
                            id="total_number">{{ $inquiries->total() }}</span> {{ $helper->t('inquiries') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- <form action="{{ route('LaravelCmsPluginInquiry.index') }}" method="POST" id="del_form">
<input type="hidden" name="_method" value="DELETE">
<input type="hidden" name="_token" value="{{ csrf_token() }}">
</form> --}}
<script>
    function confirmDelete(id){
        if ( !confirm("{{$helper->t('delete_message')}}") ) {
            return false;
        }

        $.ajax({
            url : "{{route('LaravelCmsPluginInquiry.index',[],false)}}/" + id,
            type: 'DELETE',
            data : {
                _token: "{{ csrf_token() }}",
                response_type: "json"
            },
            // contentType: false,
            // cache: false,
            // processData:false,
            dataType: 'json',
            success: function (data) {
                console.log('Submission was successful.');
                //console.log(data);
                if ( data.success ){
                    $('#inquiry-'+ id).fadeOut('slow');
                    $('#total_number').text( $('#total_number').text()-1 );
                } else {
                    alert('Error: ' + data.error_message);
                }
            },
            error: function (data) {
                console.log('laravel-cms-inquiry-delete : An error occurred.');
                console.log(data);
            },
        }).done(function(data){
            // console.log('laravel-cms-inquiry-delete submitted');
            // console.log(data);
        });

        return false;
    }


    $(function(){
        $('.inquiries a.delete-link').click(function(e){
            e.preventDefault();
            var id = $(this).data('id');
            confirmDelete(id);
        })
    });

</script>
@endsection
