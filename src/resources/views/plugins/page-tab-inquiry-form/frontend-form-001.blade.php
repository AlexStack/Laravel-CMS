<a name="inquiry_form"></a>

<form method="POST" action="{{route('LaravelCmsPluginInquiry.submitForm',null,false)}}" accept-charset="UTF-8"
    id="laravel-cms-inquiry-form">

    <input type="hidden" name="response_type" value="json" />

    {!! $dynamic_inputs !!}

</form>


{!! $gg_recaptcha !!}

<!-- Start of the laravel-cms-inquiry-form script -->
<script>
    $("#laravel-cms-inquiry-form").submit(function(event){
    event.preventDefault();
    if( typeof(grecaptcha) != 'undefined'){
        var response = grecaptcha.getResponse();
        if(response.length == 0) {
            //alert('Google recaptcha not ticked, no ajax');
            return false;
        }
    }

    $('#laravel-cms-inquiry-form button[type="submit"]').attr("disabled", "disabled").append('<i class="fas fa-spinner fa-spin ml-2"></i>');

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

                $('#laravel-cms-inquiry-form button[type="submit"]').removeAttr("disabled");
                $('#laravel-cms-inquiry-form button[type="submit"] i.fa-spinner').remove();
            }

        },
        error: function (data) {
            $('#laravel-cms-inquiry-form .error_message').html('Error: ' + data.responseJSON.message);
            $('#laravel-cms-inquiry-form button[type="submit"]').removeAttr("disabled");
            $('#laravel-cms-inquiry-form button[type="submit"] i.fa-spinner').remove();

            console.log('laravel-cms-inquiry-form : An error occurred.');
            console.log(data);
        },
    }).done(function(data){ //
        console.log('laravel-cms-inquiry-form submitted');
        //console.log(data);
    });
});
</script>
<!-- End of the laravel-cms-inquiry-form script -->
