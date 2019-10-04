@extends($helper->bladePath('includes.layout','b'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md">

            {!! Form::model($setting, ['route' => ['LaravelCmsAdminSettings.update', $setting->id], 'method' => "PUT",
            'id'=>'cms_setting_form']) !!}

            @include($helper->bladePath('includes.setting-form','b'))

            <div class="row">
                <div class="col-md">
                    @include($helper->bladePath('includes.submit-button','b'))
                </div>
                <div class="col-md-auto text-right">
                    <button type="button" class="btn btn-danger" onclick="return confirmDelete(form);"><i
                            class="fas fa-trash-alt mr-2"></i>{{$helper->t('delete')}}</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
<script>
    function confirmDelete(f){
        var del_msg = "Confirm to delete?";
        if ( confirm(del_msg) ) {
            f._method.value  = 'DELETE';
            f.action = "{{route('LaravelCmsAdminSettings.destroy', $setting->id)}}";
            f.submit();
        }
    }
</script>





<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.48.4/codemirror.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.48.4/codemirror.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.48.4/mode/javascript/javascript.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/js-beautify/1.10.2/beautify.min.js"></script>

<style>
    .CodeMirror {
        border: 1px solid #eee;
        height: auto;
    }
</style>

<script>
    if ( $(".input-param_value").is('textarea')){

        if ( $(".input-param_value").val().indexOf("}") != -1 && $(".input-param_value").val().indexOf(":") !== -1 ){
            $(".input-param_value").val(js_beautify($(".input-param_value").val(), { indent_size: 4 }));
        }

        var editor = CodeMirror.fromTextArea(document.getElementsByClassName("input-param_value")[0], {
            lineNumbers: true,
            //readOnly：false,
            styleActiveLine: true,
            mode: 'application/json',
            matchBrackets: true,
            lineWrapping: true,
            htmlMode: true,
        });
}

if ( $(".input-input_attribute").val().indexOf("}") !== -1 && $(".input-input_attribute").val().indexOf(":") !== -1 ){
    $(".input-input_attribute").val(js_beautify($(".input-input_attribute").val(), { indent_size: 4 }));

    editor = CodeMirror.fromTextArea(document.getElementsByClassName("input-input_attribute")[0], {
            lineNumbers: true,
            //readOnly：false,
            styleActiveLine: true,
            mode: 'application/json',
            matchBrackets: true,
            lineWrapping: true,
            htmlMode: true,
    });
}
</script>

@endsection
