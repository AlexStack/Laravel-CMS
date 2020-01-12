@extends($helper->bladePath('includes.layout'))

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md pb-5 page-content">

            <div class="text-center main-title">
                <h1>{{$page->title}}</h1>
            </div>

            <div class="row justify-content-center">
                {{-- search form --}}
                <div class="col-md-6 col-sm-11">
                    {!! Form::model($_GET, [ 'method' => "GET", 'id'=>'search_form' ]) !!}

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i></span>
                        </div>
                        <div class="custom-file">
                            <input type="text" class="form-control" placeholder="Keyword" aria-label="Keyword"
                                pattern="[^()/><\][\\\x22,;|'?+=\-_]+" required="required"
                                value="{{$_REQUEST['keyword'] ?? ''}}" aria-describedby="basic-addon2" name="keyword"
                                id="keyword" />
                        </div>
                        <div class="input-group-append">
                            <button class="btn btn-secondary search-btn" type="submit">{{$helper->t('search')}}</button>
                        </div>
                    </div>
                    <input name="tag" class="tag" type="hidden" value="{{$_REQUEST['tag'] ?? ''}}" />
                    {{ Form::close() }}
                </div>
                {{-- clear search terms --}}
                @if ( isset($_REQUEST['tag']) && trim($_REQUEST['tag']) != '')
                <div class="col-md-auto pt-2 pb-3">
                    <span class="search-tag">{{$helper->t('category,tag')}}
                        <a href="#" onclick="$('#search_form .tag').val('');$('#search_form').submit();return false;">
                            <i class="far fa-times-circle mr-1">
                            </i>{{$_REQUEST['tag']}}</a>
                    </span>
                </div>
                @endif


            </div>

            @if ( !empty($search_results) )
            <div class="search-results">
                @include($helper->bladePath('includes.sub-page-cards'), ['sub_pages' =>
                $search_results, 'card_class'=>'col-md-4 mb-4', 'img_width'=>$helper->s('file.small_image_width'),
                'img_height'=>$helper->s('file.small_image_height') ])
            </div>


            <div class="row justify-content-center search-pagination">
                <div class="col-md-auto  justify-content-center pagination">
                    {{ $search_results->appends(['keyword' =>$_REQUEST['keyword']??null, 'tag' =>$_REQUEST['tag']??null])->links() }}
                </div>
                <div class="w-100"></div>
                <div class="col-md-auto text-center">
                    <div class="total">{{ $helper->t('total') }} <span
                            id="total_number">{{ $search_results->total() }}</span>
                        {{ $helper->t('pages') }}</div>
                </div>
            </div>
            @endif

            @include($helper->bladePath('includes.breadcrumb'))


        </div>
    </div>
</div>

@if ( isset($_REQUEST['keyword']))
<script src="https://cdn.jsdelivr.net/npm/mark.js@8.11.1/dist/jquery.mark.min.js"></script>
<script>
    $(function(){
    $(".search-results").unmark({
        done: function() {
            $(".search-results").mark('{{$_REQUEST['keyword']}}');
        }
    });
});
</script>
@endif

@endsection
