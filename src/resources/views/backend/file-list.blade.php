@extends('laravel-cms::' . $helper->s('template.backend_dir') . '.includes.layout')

@section('content')
<script>
    document.write("<style> .file-icon {height: {{$helper->s('file.small_image_height')}}px; vertical-align: middle;}</style>");
    if ( window.location.href.indexOf('editor_id=textarea') != -1 ) {
        $('.top-header').hide();
    }
</script>

<div class="container">
    <div class="row justify-content-center mt-2">
        <div class="col-md">

            <div class="row justify-content-center upload-form">
                <div class="col-md-4">
                    {!! Form::model($_GET, ['route' => ['LaravelCmsAdminFiles.store'], 'method' => "POST",
                    'files'=>true])
                    !!}

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroupFileAddon01">{{$helper->t('file')}}</span>
                        </div>
                        <div class="custom-file">
                            <input type="file" name="files[]" class="custom-file-input" id="inputGroupFile01"
                                onchange="form.submit()" aria-describedby="inputGroupFileAddon01" multiple />
                            <label class="custom-file-label" for="inputGroupFile01">Choose file</label>
                        </div>
                        <div class="input-group-append">
                            <button class="btn btn-secondary" type="submit"
                                id="inputGroupFileAddon04">{{$helper->t('upload')}}</button>
                        </div>
                    </div>


                    <input name="editor_id" type="hidden" value="{{ $_REQUEST['editor_id'] ?? ''}}" />
                    {{ Form::close() }}
                </div>

                @if ( isset($_REQUEST['editor_id']) && strlen($_REQUEST['editor_id'])>3)
                <div class="col-md-5">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroupFileAddon02">
                                <i class="fas fa-image"></i></span>
                        </div>
                        <div class="custom-file">
                            <input type="text" class="form-control" placeholder="Image URL" aria-label="Username"
                                aria-describedby="basic-addon1" name="image_url" id="image_url" />
                        </div>
                        <div class="input-group-append">
                            <button class="btn btn-secondary" type="button"
                                onclick="return insertRemoteUrl('#image_url')">
                                {{$helper->t('insert')}}</button>
                        </div>
                    </div>
                </div>
                @endif

                {{-- search form --}}
                <div class="col-md-3">
                    {!! Form::model($_GET, ['route' => ['LaravelCmsAdminFiles.index'], 'method' => "GET",
                    'files'=>true])
                    !!}

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i></span>
                        </div>
                        <div class="custom-file">
                            <input type="text" class="form-control" placeholder="Keyword" aria-label="Keyword"
                                value="{{$_REQUEST['keyword'] ?? ''}}" aria-describedby="basic-addon2" name="keyword"
                                id="keyword" />
                        </div>
                        <div class="input-group-append">
                            <button class="btn btn-secondary" type="submit"
                                id="inputGroupFileAddon05">{{$helper->t('search')}}</button>
                        </div>
                    </div>
                    <input name="editor_id" type="hidden" value="{{ $_REQUEST['editor_id'] ?? ''}}" />
                    {{ Form::close() }}
                </div>

            </div>

            <div class="row files">
                @foreach ($files as $file)
                <div class="col-sm text-center text-truncate mb-5 file">

                    @if ( $file->is_image)
                    <div class="file-icon">
                        <a href="{{ route('LaravelCmsAdminFiles.show',['file'=>$file->id, 'generate_image'=>'yes', 'width'=>$helper->s('file.big_image_width'), 'height'=>$helper->s('file.big_image_height')]) }}"
                            target="_blank"
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

                        <a href="#" onclick="return confirmDelete({{$file->id}})" class="del">D<i
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

                        <a href="#" onclick="return confirmDelete({{$file->id}})" class="del">D<i
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

            <div class="row justify-content-center upload-form">
                <div class="col-md-auto pagination">
                    {{ $files->appends(['editor_id' =>$_REQUEST['editor_id']??null, 'keyword' =>$_REQUEST['keyword']??null])->links() }}
                </div>
            </div>
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

    function insertRemoteUrl(url_id)  {
        var link = $.trim($(url_id).val());
        var external_class = ( link.match(/http:|https:/) == null || link.indexOf(location.hostname) != -1 ) ? '' : 'external-link';
        if ( link.match(/\.(jpeg|jpg|gif|png|svg|bmp|webp)$/) != null ){
            var html_str = '<img src="' + link + '" class="img-fluid content-img '+ external_class + '" />';
        } else {
            var html_str = ' <a href="' + link + '" class="content-file '+ external_class + '" target="_blank"><i class="fas fa-link mr-1"></i>' + link + '</a> ';
        }

        return handleInsertToEditor(html_str);
        //return false;
    }

    function handleInsertToEditor(html_str)   {
        if ( window.parent ){
            var editor_page = window.parent;
        } else if ( window.opener ){
            var editor_page = window.opener;
        }else {
            console.log('editor_page callback failed');
            return false;
        }
        editor_page.insertHtmlToEditor("{{ $_GET['editor_id'] ?? 'textarea.input-main_content'}}", html_str);
        editor_page.hideIframeModal('#iframe-modal');
        return true;
    }

    if ( window.location.href.indexOf('editor_id=textarea') != -1 ) {
        $('a.del').hide();
        $('.files a.preview_link').click(function(e)
        {
            e.preventDefault();


            var link = $(this).attr('href');
            if ( link.indexOf('generate_image') != -1 ){
                var ajax_data = $.ajax({
                            type: "GET",
                            url: link + '&return_url=yes',
                            success: function(response) {
                                //console.log(response);
                            },
                            cache: false,
                            async: false
                        });
                link = ajax_data.responseText; // ajax sync
            }

            var html_str = '<img src="' + link + '" class="img-fluid content-img" />';

            if ( $(this).hasClass('not_image')){
                var link_txt = $(this).attr('title');
                if ( $(this).hasClass('icon')){
                    link_txt = '<i class="' + $(this).find('i:first').attr('class') + ' mr-1"></i>' + link_txt;
                }
                html_str = '&nbsp;<a href="' + link + '" class="content-file" target="_blank">' + link_txt + '</a>&nbsp;';
            }


            handleInsertToEditor(html_str);

        });
    }
</script>
@endsection
