<div id="search-results"></div>
@php
$form_enabled_label = $helper->t('form_enabled');
if ( isset($tab_data->form_enabled) ){
foreach( $tab_data->toArray() as $k => $v )
{
if ( !isset($page[$k]) ) {
// Set default value on edit page
$page[$k] = $v;
}
}
$form_enabled_label .= "(ID:$tab_data->id)" . ' <a
    href="' . route('LaravelCmsPluginInquiry.index','page_id='.$page->id) . '"
    class="btn btn-outline-info btn-sm ml-3"><i class="fas fa-user-edit  mr-1"></i>'
    .
    $helper->t('view, inquiries') .
    '</a>';
}
@endphp



@include($helper->bladePath('includes.form-input','b'), ['name' =>
"form_enabled", 'label'=> $form_enabled_label, 'type'=>'select', 'options'=>['0' => $helper->t('disable'), '1' =>
$helper->t('enable')] ])


@include($helper->bladePath('includes.form-input','b'), ['name' =>
"default_setting_id"])


@include($helper->bladePath('includes.form-input','b'), ['name' =>
"form_layout"])

@include($helper->bladePath('includes.form-input','b'), ['type'=>'textarea',
'name' => "display_form_fields"])

@include($helper->bladePath('includes.form-input','b'), ['name' =>
"success_title"])

@include($helper->bladePath('includes.form-input','b'), ['type'=>'textarea',
'name' => "success_content"])

@include($helper->bladePath('includes.form-input','b'), ['name' =>
"google_recaptcha_enabled", 'type'=>'select', 'options'=>['' => $helper->t('default'),'0' => $helper->t('disable'),
'1'=> $helper->t('enable')] ])


@include($helper->bladePath('includes.form-input','b'), ['name' =>
"google_recaptcha_css_class"])

@include($helper->bladePath('includes.form-input','b'), ['name' =>
"google_recaptcha_no_tick_msg"])



<script>
    $("#show-inquiries").click(function(event){
    event.preventDefault();
    $.ajax({
        url : admin_route + '/search-inquiries',
        type: 'POST',
        data : {
            _token: "{{ csrf_token() }}",
            page_id: "{{ isset($page) ? $page->id : ''}}"
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

@if ( $helper->s('inquiry.default_enable') == '1' && ! isset($page['title']))
    $('.input-form_enabled').val(1).change();
@endif
</script>
