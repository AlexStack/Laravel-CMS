@extends($helper->bladePath('includes.layout','b'))

@section('content')

<div class="container">
    <div class="row justify-content-center mt-2">
        <div class="col-md">
            <ul id="sortableList" class="list-group all-pages">
                @forelse ($all_pages as $item)
                @if ( isset($_GET['hide_depth']) && $item->depth >= $_GET['hide_depth'])
                @continue
                @endif
                <li class="list-group-item list-group-item-action" id="page-{{$item->id}}">
                    <i class="fas fa-arrows-alt text-light handle"></i>
                    @php
                    if ( $item->depth ){
                    echo str_repeat("⎯⎯⎯", $item->depth);
                    }
                    if ( trim($item->redirect_url) != '' ) {
                    $color_class = 'text-success';
                    } else {
                    $color_class = 'text-secondary';
                    }
                    if ( $item->slug == 'homepage'){
                    $color_class = 'text-primary';
                    }
                    if ( $item->menu_enabled) {
                    if ( $item->depth == 0 ){
                    $icon = '<i class="fas fa-list-alt ml-1 ' . $color_class . ' "></i>';
                    } elseif ( $item->depth == 1 ){
                    $icon = '<i class="fas fa-list-ul ml-1 ' . $color_class . ' "></i>';
                    } else {
                    $icon = '<i class="fas fa-stream ml-1 ' . $color_class . ' "></i>';
                    }

                    } else {
                    $icon = '<i class="far fa-file ml-1 ' . $color_class . ' "></i>';
                    }
                    if ( $item->slug == 'homepage'){
                    $icon = '<i class="fas fa-home ml-1 ' . $color_class . ' "></i>';
                    }
                    @endphp

                    {!! $icon !!}
                    <a href="./pages/{{$item->id}}/edit" title="{{$helper->t('sort_value')}} {{$item->sort_value ?? 0}}"
                        class="{{ $item->menu_enabled ? 'menu_enabled': ''}}">
                        @if ( $item->menu_title)
                        [ {{$item->menu_title}} ] -
                        @endif
                        {{$item->title}}
                    </a>

                    <a href="./pages/{{$item->id}}/edit" class="text-secondary"><i class="far fa-edit ml-3"></i></a>

                    <a href="{{$helper->url($item)}}" class="{{$color_class}}" target="_blank"><i
                            class="far fa-eye ml-3"></i></a>

                    @if ( $item->menu_enabled)
                    <a href="{{ route('LaravelCmsAdminPages.create', ['parent_id' => $item->id, 'menu_enabled'=>0]) }}"
                        class="text-secondary"><i class="far fa-plus-square ml-3"></i></a>
                    @endif

                    @if ( $item->slug == 'homepage')
                    <span clas="create_top_new_page">
                        <a class='btn btn-outline-primary btn-sm ml-3'
                            href='{{route('LaravelCmsAdminPages.create',['switch_nav_tab'=>'settings'])}}'
                            role='button'>
                            <i class='fas fa-plus-circle mr-1'></i>{{$helper->t('create_new_page')}}</a>
                    </span>
                    @else
                    <a href="#del-{{$item->id}}" class="float-right delete-link" data-id="{{$item->id}}"
                        title="{{$helper->t('delete')}}" data-title="{{$item->title}}"><i
                            class="far fa-trash-alt"></i></a>
                    @endif
                </li>
                @empty
                <li class="list-group-item list-group-item-action">No Page yet, <a
                        href="{{ route('LaravelCmsAdminPages.create') }}">Create a new page</a> </a>
                    @endforelse
            </ul>

        </div>
    </div>
</div>
<script>
    function confirmDelete(id, title){
        if ( !confirm("{{$helper->t('delete_message')}} " + title) ) {
            return false;
        }

        $.ajax({
            url : "{{route('LaravelCmsAdminPages.index',[],false)}}/" + id,
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
                    $('#page-'+ id).fadeOut('slow');
                } else {
                    alert('Error: ' + data.error_message);
                }
            },
            error: function (data) {
                console.log('laravel-cms-page-delete : An error occurred.');
                console.log(data);
            },
        }).done(function(data){
            // console.log('laravel-cms-page-delete submitted');
            // console.log(data);
        });

        return false;
    }


    $(function(){
        $('.all-pages a.delete-link').click(function(e){
            e.preventDefault();
            var id = $(this).data('id');
            confirmDelete(id, $(this).data('title'));
        })
    });

</script>
@endsection
