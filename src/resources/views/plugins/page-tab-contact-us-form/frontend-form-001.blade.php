contact us form contact us form

contact us form

{!! Form::model($_GET, ['route' => ['LaravelCmsPluginInquiry.submitForm'], 'method' => "POST", 'files'=>true, 'id'=>'laravel-cms-inquiry-form']) !!}

<input type="hidden" name="page_id" value="{{$page->id}}" />
<input type="hidden" name="page_title" value="{{$page->title}}" />
<input type="hidden" name="result_type" value="json" />

{!! $dynamic_inputs !!}

{{-- <div id="laravel-cms-inquiry-form-results">
    <div class="error_message"></div>
    <button type="submit" class="btn btn-primary btn-submit"><i class="fas fa-save mr-2"></i>Submit</button>
</div> --}}
{{ Form::close() }}

<script>
$("#laravel-cms-inquiry-form").submit(function(event){
    event.preventDefault();
    $.ajax({
        url : $(this).attr("action"),
        type: $(this).attr("method"),
        data : new FormData(this),
		contentType: false,
		cache: false,
        processData:false,
        dataType: 'json',
        success: function (data) {
            //console.log('Submission was successful.');
            //console.log(data);
            $("#laravel-cms-inquiry-form-results").html(data.success_content);
        },
        error: function (data) {
            $('#laravel-cms-inquiry-form .error_message').html('Error: ' + data.responseJSON.message);
            console.log('laravel-cms-inquiry-form : An error occurred.');
            console.log(data);
        },
    }).done(function(data){ //
        console.log('laravel-cms-inquiry-form submitted');
    });
});
</script>
