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

$form_enabled_label .= ' <a href="#" class="btn btn-outline-secondary btn-sm ml-3 advanced-settings"><i
        class="fas fa-cogs mr-1"></i>'
    .
    $helper->t('advanced, settings') .
    '</a>';
@endphp



@include($helper->bladePath('includes.form-input','b'), ['name' =>
"form_enabled", 'label'=> $form_enabled_label, 'type'=>'select', 'options'=>['0' => $helper->t('disable'), '1' =>
$helper->t('enable')] ])


<div id="inquiry-form-advanced-settings">
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
    "mail_subject"])
    @include($helper->bladePath('includes.form-input','b'), ['name' =>
    "mail_to"])

    @include($helper->bladePath('includes.form-input','b'), ['name' =>
    "google_recaptcha_enabled", 'type'=>'select', 'options'=>['' => $helper->t('default'),'0' => $helper->t('disable'),
    '1'=> $helper->t('enable')] ])


    @include($helper->bladePath('includes.form-input','b'), ['name' =>
    "google_recaptcha_css_class"])

    @include($helper->bladePath('includes.form-input','b'), ['name' =>
    "google_recaptcha_no_tick_msg"])



</div>

<script>
    $(".advanced-settings").click(function(event){
        event.preventDefault();
        $( "#inquiry-form-advanced-settings" ).toggle( "slow", function() {
        // Animation complete.
        });
    });

@if ( !isset($tab_data->id) || $helper->s('inquiry.default_setting_id') != $tab_data->id)
    if ( location.href.indexOf('show_advanced_settings=yes') == -1){
        $( "#inquiry-form-advanced-settings" ).hide();
    }
@endif

@if ( $helper->s('inquiry.enable_form_by_default') == '1' && ! isset($page['title']))
    $('.input-form_enabled').val(1).change();
@endif


@if ( isset($tab_data->default_setting_id) && $tab_data->default_setting_id>0)
    var default_setting_link = '{{route("LaravelCmsPluginInquiry.show",["inquiry"=>$tab_data->default_setting_id, "go_setting_section"=>"yes"])}}';
@else
    var default_setting_link = '{{route("LaravelCmsPluginInquiry.show",["inquiry"=>$helper->s('inquiry.default_setting_id'), "go_setting_section"=>"yes"])}}';
@endif
$('.label-default_setting_id').append(' <a href="' + default_setting_link + '" target="_blank" class="text-info small"><i class="fas fa-external-link-square-alt"></i></a>');
</script>
