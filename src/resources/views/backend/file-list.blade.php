@extends($helper->bladePath('includes.layout','b'))

@section('content')
<script>
    document.write("<style> .file-icon {height: {{$helper->s('file.small_image_height')}}px; vertical-align: middle;} .file-icon img{max-height: {{$helper->s('file.small_image_height')}}px;}</style>");
    if ( window.location.href.indexOf('editor_id=textarea') != -1 ||  window.location.href.indexOf('editor_id=input.') != -1 ) {
        $('.top-header').hide();
    }
</script>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md">

            <div class="row justify-content-center header-forms">
                {{-- upload form --}}
                <div class="col-md-4">
                    {!! Form::model($_GET, ['route' => ['LaravelCmsAdminFiles.store'], 'method' => "POST",
                    "id"=>"file_upload_form",
                    'files'=>true])
                    !!}

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroupFileAddon01">
                                <i class="fas fa-upload"></i></span>
                        </div>
                        <div class="custom-file">
                            <input type="file" name="files[]" class="custom-file-input" id="inputGroupFile01"
                                required="required" onchange="$('#file_upload_form').trigger('submit');"
                                aria-describedby="inputGroupFileAddon01" multiple />
                            <label class="custom-file-label" for="inputGroupFile01">Choose file</label>
                        </div>
                        <div class="input-group-append">
                            <button class="btn btn-secondary" type="submit"
                                id="upload_button">{{$helper->t('upload')}}</button>
                        </div>
                    </div>


                    <input name="editor_id" type="hidden" value="{{ $_REQUEST['editor_id'] ?? ''}}" />
                    {{ Form::close() }}
                </div>

                {{-- insert remote url to editor --}}
                @if ( isset($_REQUEST['editor_id']) && strpos($_REQUEST['editor_id'],'textarea.') !== false)
                <div class="col-md-5">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroupFileAddon02">
                                <i class="fas fa-image"></i></span>
                        </div>
                        <div class="custom-file">
                            <input type="text" class="form-control" placeholder="Image URL" aria-label="Username"
                                required="required" aria-describedby="basic-addon1" name="image_url" id="image_url" />
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
                                required="required" value="{{$_REQUEST['keyword'] ?? ''}}"
                                aria-describedby="basic-addon2" name="keyword" id="keyword" />
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

            {{-- show files --}}
            <div class="row files">
                @foreach ($files as $file)
                <div class="col-sm text-center text-truncate mb-5 file" id="file-{{$file->id}}">

                    @if ( $file->is_image)
                    <div class="file-icon">
                        <a href="{{ route('LaravelCmsAdminFiles.show',['file'=>$file->id, 'generate_image'=>'yes', 'width'=>$helper->s('file.large_image_width'), 'height'=>$helper->s('file.large_image_height')]) }}"
                            target="_blank" data-id="{{$file->id}}"
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
                            O<i class="fas fa-external-link-alt ml-1 small text-secondary"></i></a>


                        <a href="{{ route('LaravelCmsAdminFiles.show',['file'=>$file->id, 'generate_image'=>'yes', 'width'=>$helper->s('file.large_image_width'), 'height'=>$helper->s('file.large_image_height')]) }}"
                            class="preview_link is_image" target="_blank" title="{{ $helper->t('large_image') }}">
                            L<i class="fas fa-external-link-alt ml-1 small text-secondary"></i></a>

                        <a href="{{ route('LaravelCmsAdminFiles.show',['file'=>$file->id, 'generate_image'=>'yes', 'width'=>$helper->s('file.middle_image_width'), 'height'=>$helper->s('file.middle_image_height')]) }}"
                            class="preview_link is_image" target="_blank" title="{{ $helper->t('middle_image') }}">
                            M<i class="fas fa-external-link-alt ml-1 small text-secondary"></i></a>

                        <a href="{{$helper->imageUrl($file, $helper->s('file.small_image_width'), $helper->s('file.small_image_height')) }}"
                            class="preview_link is_image" id="small-img-{{$file->id}}" target="_blank"
                            title="{{ $helper->t('small_image') }}">
                            S<i class="fas fa-external-link-alt ml-1 small text-secondary"></i></a>

                        <a href="#del-{{$file->id}}" class="delete-link" data-id="{{$file->id}}">D<i
                                class="far fa-trash-alt ml-1 small text-secondary"></i></a>

                    </div>
                    @else
                    <div class="file-icon">
                        <h1><a href="{{$helper->imageUrl($file, 'original','original') }}" target="_blank"
                                class="preview_link not_image icon" title="{{$file->filename}}">
                                {!! $helper->fileIconCode($file->suffix) !!}
                            </a></h1>
                    </div>
                    <div class="text-info links">
                        <a href="{{$helper->imageUrl($file, 'original','original') }}" target="_blank"
                            title="{{$file->filename}}" class="preview_link not_image">{{strtoupper($file->suffix)}}
                            {{$helper->t('file')}}<i class="fas fa-external-link-alt ml-1 small text-secondary"></i></a>
                         {{($file->filesize/1024 > 1000) ? round($file->filesize/1024/1024,2) . ' MB' : (($file->filesize/1024 > 10) ? round($file->filesize/1024) . ' KB' : round($file->filesize/1024, 1) . ' KB') }}
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
                <div class="col-md-auto  justify-content-center pagination">
                    {{ $files->appends(['editor_id' =>$_REQUEST['editor_id']??null, 'keyword' =>$_REQUEST['keyword']??null])->links() }}
                </div>
                <div class="w-100"></div>
                <div class="col-md-auto text-center">
                    <div class="total">{{ $helper->t('total') }} <span id="total_number">{{ $files->total() }}</span>
                        {{ $helper->t('files') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- <form action="{{ route('LaravelCmsAdminFiles.index') }}" method="POST" id="del_form">
<input type="hidden" name="_method" value="DELETE">
<input type="hidden" name="_token" value="{{ csrf_token() }}">
</form> --}}
<script>
    // function confirmDelete(id){
    //     var f = document.getElementById('del_form');
    //     var del_msg = "Confirm to delete?";
    //     if ( confirm(del_msg) ) {
    //         f._method.value  = 'DELETE';
    //         f.action = "{{route('LaravelCmsAdminFiles.index')}}/" + id;
    //         f.submit();
    //     }
    //     return false;
    // }


    function confirmDelete(id){
        if ( !confirm("{{$helper->t('delete_message')}}") ) {
            return false;
        }

        $.ajax({
            url : "{{route('LaravelCmsAdminFiles.index', null, false)}}/" + id,
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
                    $('#file-'+ id).fadeOut('slow');
                    $('#total_number').text( $('#total_number').text()-1 );
                } else {
                    alert('Error: ' + data.error_message);
                }
            },
            error: function (data) {
                console.log('laravel-cms-file-delete : An error occurred.');
                console.log(data);
            },
        }).done(function(data){
            // console.log('laravel-cms-file-delete submitted');
            // console.log(data);
        });

        return false;
    }

    function insertRemoteUrl(url_id)  {
        var link = $.trim($(url_id).val());
        var external_class = ( link.match(/http:|https:/) == null || link.indexOf(location.hostname) != -1 ) ? '' : 'external-link';
        if ( link.toLowerCase().match(/\.*(jpeg|jpg|gif|png|svg|bmp|webp|image|img|pic|photo|picture)/) != null || link.slice(-5).indexOf('.') == -1 ){
            var html_str = '<img src="' + link + '" class="img-fluid content-img '+ external_class + '" />';
        } else {
            var html_str = '&nbsp;<a href="' + link + '" class="content-file '+ external_class + '" target="_blank"><i class="fas fa-link mr-1"></i>' + link + '</a>&nbsp;';
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

    function handleInsertToUploadField(html_str, file_id)   {
        if ( window.parent ){
            var editor_page = window.parent;
        } else if ( window.opener ){
            var editor_page = window.opener;
        }else {
            console.log('handleInsertToUploadField: editor_page callback failed');
            return false;
        }

        editor_page.insertToUploadField("{{ $_GET['editor_id'] ?? 'input.input-main_image_id'}}", html_str, file_id);
        editor_page.hideIframeModal('#iframe-modal');
        return true;
    }

    $(function(){
        // for textarea editor
        if ( window.location.href.indexOf('editor_id=textarea') != -1 ) {
            $('a.delete-link').hide();
            if (jQuery(window).width() > 800) {
                $('.header-forms').addClass('sticky-top').addClass('bg-white').addClass('pt-2');
            }
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

        // for upload input field with a hidden input_id
        if ( window.location.href.indexOf('editor_id=input.') != -1 ) {
            $('.files .file .links').hide();
            if (jQuery(window).width() > 800) {
                $('.header-forms').addClass('sticky-top').addClass('bg-white').addClass('pt-2');
            }
            $('.files a.preview_link').click(function(e)
            {
                e.preventDefault();

                var file_id = $(this).data('id');
                var link = $('#small-img-'+file_id).attr('href');

                var html_str = '<img src="' + link + '" class="img-fluid img-thumbnail" />';

                if ( $(this).hasClass('not_image')){
                    alert('It is not an image!');
                    return false;
                }

                handleInsertToUploadField(html_str,file_id);
            });
        }

        $('.files a.delete-link').click(function(e){
            e.preventDefault();
            var id = $(this).data('id');
            confirmDelete(id);
        });

        $( "#file_upload_form" ).submit(function( e ) {
            $('#upload_button').addClass('disabled').html('<i class="fas fa-spinner fa-spin"></i>');
            //e.preventDefault();
            return true;
        });
    });


</script>
@endsection
