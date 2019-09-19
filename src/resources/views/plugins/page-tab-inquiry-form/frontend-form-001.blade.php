<a name="inquiry_form"></a>
{!! Form::model($_GET, ['route' => ['LaravelCmsPluginInquiry.submitForm'], 'method' => "POST", 'files'=>true,
'id'=>'laravel-cms-inquiry-form']) !!}

<input type="hidden" name="response_type" value="json" />

{!! $dynamic_inputs !!}

{{-- <div id="laravel-cms-inquiry-form-results">
    <div class="error_message"></div>
    <button type="submit" class="btn btn-primary btn-submit"><i class="fas fa-save mr-2"></i>Submit</button>
</div> --}}
{{ Form::close() }}

{!! $gg_recaptcha !!}

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
            console.log('Submission was successful.');
            //console.log(data);
            if ( data.success ){
                $("#laravel-cms-inquiry-form .form-group").fadeOut('slow');
                $("#laravel-cms-inquiry-form-results").html(data.success_content);

            } else {
                $('#laravel-cms-inquiry-form .error_message').html('Error: ' + data.error_message);
            }

        },
        error: function (data) {
            $('#laravel-cms-inquiry-form .error_message').html('Error: ' + data.responseJSON.message);
            console.log('laravel-cms-inquiry-form : An error occurred.');
            console.log(data);
        },
    }).done(function(data){ //
        console.log('laravel-cms-inquiry-form submitted');
        console.log(data);
    });
});
</script>
