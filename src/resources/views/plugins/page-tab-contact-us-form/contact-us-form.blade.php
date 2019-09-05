contact us form contact us form contact us form
onetime token= {{$page_model->id}} -
<button id="show-inquiries" type="button">Show Inquiries</button>
<div  id="search-results"></div>
@php
    if ( isset($tab_data->success_title) ){
        foreach( $tab_data->toArray() as $k => $v )
        {
            if ( !isset($page_model[$k]) )  {
                // Set default value on edit page
                $page_model[$k] = $v;
            }
        }
    }
@endphp



@include('laravel-cms::' . config('laravel-cms.template_backend_dir') .  '.includes.form-input', ['name' => "form_enabled", 'type'=>'select', 'label'=>'Form Enabled', 'options'=>['0' => 'Disable', '1' => 'Enable Contact Us Form for this page'] ])


@include('laravel-cms::' . config('laravel-cms.template_backend_dir') .  '.includes.form-input', ['name' => "default_setting_id"])


@include('laravel-cms::' . config('laravel-cms.template_backend_dir') .  '.includes.form-input', ['name' => "form_layout"])

@include('laravel-cms::' . config('laravel-cms.template_backend_dir') .  '.includes.form-input', ['type'=>'textarea', 'name' => "display_form_fields"])

@include('laravel-cms::' . config('laravel-cms.template_backend_dir') .  '.includes.form-input', ['name' => "success_title"])

@include('laravel-cms::' . config('laravel-cms.template_backend_dir') .  '.includes.form-input', ['type'=>'textarea', 'name' => "success_content"])

@include('laravel-cms::' . config('laravel-cms.template_backend_dir') .  '.includes.form-input', ['name' => "google_recaptcha_enabled", 'type'=>'select', 'label'=>'Google recaptcha', 'options'=>['0' => 'Disable', '1' => 'Enable'] ])


@include('laravel-cms::' . config('laravel-cms.template_backend_dir') .  '.includes.form-input', ['name' => "google_recaptcha_css_class"])

@include('laravel-cms::' . config('laravel-cms.template_backend_dir') .  '.includes.form-input', ['name' => "google_recaptcha_no_tick_msg"])



<script>
$("#show-inquiries").click(function(event){
    event.preventDefault();
    $.ajax({
        url : '/cmsadmin/search-inquiries',
        type: 'POST',
        data : {
            _token: "{{ csrf_token() }}",
            page_id: "{{$page_model->id}}"
        },
		// contentType: false,
		// cache: false,
        // processData:false,
        dataType: 'json',
        success: function (data) {
            console.log('Submission was successful.');
            //console.log(data);
            if ( data.success ){
                $("#search-results").html(data.html_content);
            } else {
                $('#laravel-cms-inquiry-form .error_message').html('Error: ' + data.error_message);
            }

        },
        error: function (data) {
            //$('#laravel-cms-inquiry-form .error_message').html('Error: ' + data.responseJSON.message);
            console.log('laravel-cms-inquiry-form : An error occurred.');
            console.log(data);
        },
    }).done(function(data){ //
        console.log('laravel-cms-inquiry-form submitted');
        console.log(data);
    });
});
</script>
