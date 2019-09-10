@extends('laravel-cms::' . $helper->s('template.backend_dir') . '.includes.layout')

@section('content')
<script>
    document.write("<style> .file-icon {height: {{$helper->s('file.small_image_height')}}px; vertical-align: middle;}</style>");
    if ( window.location.href.indexOf('insert_files_to_editor') != -1 ) {
        $('.top-header').hide();

    }
</script>

<div class="container">
    <div class="row justify-content-center mt-2">
        <div class="col-md">




            <a href="#"
                onClick="opener.insertImageToEditor('.input-main_content','http://xinxilan.org.test/storage/laravel-cms-uploads/optimized/32/32_151_101_ratio.jpg');window.close();return false;">test
                image</a>

            <div class="row files">
                @foreach ($files as $file)
                <div class="col-sm text-center text-truncate mb-5 file">

                    @if ( $file->is_image)
                    <div class="file-icon">
                        <a href="{{$helper->imageUrl($file, 'original','original') }}" target="_blank"
                            title="Size: {{($file->filesize/1024 > 1000) ? round($file->filesize/1024/1024,2) . ' MB' : round($file->filesize/1024) . ' KB' }}"
                            class="preview_link is_image">
                            <img class="img-fluid rounded"
                                src="{{$helper->imageUrl($file, $helper->s('file.small_image_width'), $helper->s('file.small_image_height')) }}"
                                alt="{{$file->filename}}" />
                        </a>
                    </div>
                    <div class="links">
                        <a href="{{$helper->imageUrl($file, 'original','original') }}" class="preview_link is_image"
                            target="_blank"
                            title="Original File Size: {{($file->filesize/1024 > 1000) ? round($file->filesize/1024/1024,2) . ' MB' : round($file->filesize/1024) . ' KB' }}">
                            O<i class="fas fa-external-link-alt ml-1 small text-secondary"></i>
                        </a>


                        <a href="{{ route('LaravelCmsAdminFiles.show',['file'=>$file->id, 'generate_image'=>'yes', 'width'=>$helper->s('file.big_image_width'), 'height'=>$helper->s('file.big_image_height')]) }}"
                            class="preview_link is_image" target="_blank" title="{{ $helper->t('large_image') }}">
                            L<i class="fas fa-external-link-alt ml-1 small text-secondary"></i>
                        </a>

                        <a href="{{ route('LaravelCmsAdminFiles.show',['file'=>$file->id, 'generate_image'=>'yes', 'width'=>$helper->s('file.middle_image_width'), 'height'=>$helper->s('file.middle_image_height')]) }}"
                            class="preview_link is_image" target="_blank" title="{{ $helper->t('middle_image') }}">
                            M<i class="fas fa-external-link-alt ml-1 small text-secondary"></i>
                        </a>

                        <a href="{{$helper->imageUrl($file, $helper->s('file.small_image_width'), $helper->s('file.small_image_height')) }}"
                            class="preview_link is_image" target="_blank" title="{{ $helper->t('small_image') }}">
                            S<i class="fas fa-external-link-alt ml-1 small text-secondary"></i>
                        </a>

                        <a href="#" onclick="return confirmDelete({{$file->id}})">D<i
                                class="far fa-trash-alt ml-1 small text-secondary"></i></a>

                    </div>
                    @else
                    <div class="file-icon">
                        <h1><a href="{{$helper->imageUrl($file, 'original','original') }}" target="_blank"
                                class="preview_link not_image icon" title="{{$file->filename}}">
                                {!! $helper->fileIconCode($file->suffix) !!}
                            </a></h1>
                    </div>
                    <div class="text-info">
                        <a href="{{$helper->imageUrl($file, 'original','original') }}" target="_blank"
                            title="{{$file->filename}}" class="preview_link not_image">{{strtoupper($file->suffix)}}
                            {{$helper->t('file')}}<i class="fas fa-external-link-alt ml-1 small text-secondary"></i></a>
                        {{($file->filesize/1024 > 1000) ? round($file->filesize/1024/1024,2) . ' MB' : ($file->filesize/1024 > 10) ? round($file->filesize/1024) . ' KB' : round($file->filesize/1024, 1) . ' KB' }}

                        <a href="#" onclick="return confirmDelete({{$file->id}})">D<i
                                class="far fa-trash-alt ml-1 small text-secondary"></i></a>
                    </div>
                    @endif
                    <div class="title">
                        {{$file->title}}
                    </div>
                </div>
                @if ( ($loop->index+1)%4 == 0)
                <div class="w-100"></div>
                @endif
                @endforeach
            </div>

            <!-- Tab panes start -->
            <style>
                .list-group-item:first-child {
                    border-top-left-radius: 0;
                    border-top-right-radius: 0;
                    border-top: 0;
                }
            </style>
            <div class="tab-content">


            </div>
            <!-- Tab panes end -->


            {{--
            <ul id="sortableList" class="list-group">
                @foreach ($settings as $item)
                    <li class="list-group-item list-group-item-action">
                        @php
                            if ( $item->enabled) {
                                $icon =  '<i class="fas fa-wrench ml-1  "></i>';
                            } else {
                                $icon =  '<i class="fas fa-hammer ml-1 "></i>';
                            }
                        @endphp

                        {!! $icon !!}
                        <a href="./settings/{{$item->id}}/edit"
            class="{{$item->enabled ? 'text-dark font-weight-bold' : 'text-secondary'}}" title="Sort Value:
            {{$item->sort_value??0}}">
            [ {{$item->category}}
            @if ( $item->page_id)
            PageID:{{$item->page_id}}
            @endif
            ] -
            {{$item->param_name}}
            </a>

            <a href="./settings/{{$item->id}}/edit" class="{{$item->enabled ? 'text-dark' : 'text-secondary'}}"><i
                    class="far fa-edit ml-1 mr-1" title="Sort Value: {{$item->sort_value??0}}"></i></a>

            <span class="abstract">
                ({!! \Illuminate\Support\Str::words($item->abstract, 20,'...') !!})
            </span>

            <a href="{{ route('LaravelCmsAdminSettings.create', ['category' => $item->category, 'page_id'=>$item->page_id, 'input_attribute'=>$item->input_attribute, 'sort_value'=>($item->sort_value-1)]) }}"
                class="text-secondary"><i class="far fa-plus-square ml-1"></i></a>

            <div class="param-value {{$item->enabled ? 'text-success' : 'text-secondary'}}">
                <i class="far fa-arrow-alt-circle-right ml-1 "></i> {{ str_limit($item->param_value, 100, '...')}}
            </div>
            </li>
            @empty
            <li class="list-group-item list-group-item-action">No Setting yet, <a
                    href="{{ route('LaravelCmsAdminSettings.create', ['category' => 'global', 'page_id'=>null, 'input_attribute'=>'{"rows":1,"required":"required"}', 'sort_value'=>1000]) }}">Create
                    a new Setting</a> </a>
                @endforeach
                </ul> --}}

        </div>
    </div>
</div>

<form action="{{ route('LaravelCmsAdminFiles.index') }}" method="POST" id="del_form">
    <input type="hidden" name="_method" value="DELETE">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
</form>
<script>
    function confirmDelete(id){
        var f = document.getElementById('del_form');
        var del_msg = "Confirm to delete?";
        if ( confirm(del_msg) ) {
            f._method.value  = 'DELETE';
            f.action = "{{route('LaravelCmsAdminFiles.index')}}/" + id;
            f.submit();
        }
        return false;
    }
    if ( window.location.href.indexOf('insert_files_to_editor') != -1 ) {
        $('.files a.preview_link').click(function(e)
        {
            e.preventDefault();

            if ( window.parent ){
                var editor_page = window.parent;
            } else if ( window.opener ){
                var editor_page = window.opener;
            }

            var link = $(this).attr('href');
            if ( $(this).attr('href').indexOf('generate_image') != -1 ){
                var ajax_data = $.ajax({
                            type: "GET",
                            url: $(this).attr('href') + '&return_url=yes',
                            success: function(response) {
                                console.log(response);
                            },
                            cache: false,
                            async: false
                        });
                link = ajax_data.responseText;
            }

            var html_str = '<img src="' + link + '" class="img-fluid content-img" />';

            if ( $(this).hasClass('not_image')){
                var link_txt = $(this).attr('title');
                if ( $(this).hasClass('icon')){
                    link_txt = '<i class="' + $(this).find('i:first').attr('class') + ' mr-1"></i>' + link_txt;
                }
                html_str = ' <a href="' + link + '" class="content-file" target="_blank">' + link_txt + '</a> ';
            }


            if ( typeof(editor_page) !== 'undefined'){
                editor_page.insertHtmlToEditor("{{ $_GET['editor_id'] ?? '.input-main_content'}}", html_str);
                editor_page.hideIframeModal('#iframe-modal');
            } else {
                console.log('editor_page callback failed');
            }


        });
    }
</script>
@endsection
