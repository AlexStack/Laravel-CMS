@include($helper->bladePath('includes.form-input','b'), ['type'=>'select', 'name' =>
"category","input_attributes"=>['required'=>'required','pattern'=>'[a-zA-Z0-9\-_]{2,60}'], 'options'=>$categories])

@include($helper->bladePath('includes.form-input','b'), ['type'=>'text', 'name' =>
"param_name","input_attributes"=>['required'=>'required','pattern'=>'[a-zA-Z0-9\-_]{2,60}']])
@if ( isset($setting->param_name) )
<div class="text-secondary small mb-2 text-help">
    <span
        class="small">{{ $helper->t('how_to_use_in_blade', ['name'=>$setting->category . '.' . $setting->param_name]) }}</span>
</div>
@endif

@if ( isset($setting->alert) )
<div class="alert alert-danger small" role="alert">
    <span>{{ $setting->alert }}</span>
</div>
@endif

@if ( isset($setting->abstract) && trim(strip_tags($setting->abstract)) != '' )
<div class="alert alert-info small abstract-text" role="alert">
    @if ( $setting->category == 'plugin' && 'standalone' == $helper->s('plugin.'.$setting->param_name.'.plugin_type') )
    <a href="../../plugins/{{$setting->param_name}}" class="text-primary mr-2">{!!
        $helper->s('plugin.'.$setting->param_name.'.tab_name') !!}</a>
    @endif
    {!! $setting->abstract !!}
</div>
@endif

@php
$attr = isset($setting) ? json_decode($helper->parseCmsStr($setting->input_attribute), TRUE) :
['required'=>'required'];
//$helper->debug($attr);
$input_type = ( isset($attr['rows']) && $attr['rows'] > 1 ) ?'textarea' : 'text';
if ( isset($attr['select_options']) && is_array($attr['select_options']) ) {
$input_type = 'select';
$select_options = $attr['select_options'];
unset($attr['select_options']);
} else {
$select_options = [];
}
@endphp

@include($helper->bladePath('includes.form-input','b'), [
'type' => $input_type,
'name' => "param_value",
'options' => $select_options,
"input_attributes" => $attr
])



@include($helper->bladePath('includes.form-input','b'), ['name' => "enabled",
'type'=>'select',
'label' => $helper->t('enable,setting'),
'options'=>['1' => $helper->t('enable'), '0' => $helper->t('disable')] ])

@include($helper->bladePath('includes.form-input','b'), ['type'=>'text', 'name' =>
"sort_value"])


@include($helper->bladePath('includes.form-input','b'), ['name' => "input_attribute",
'type'=>'textarea', "input_attributes" =>['rows'=>4]])


@include($helper->bladePath('includes.form-input','b'), ['type'=>'textarea', 'name' =>
"abstract", "input_attributes"=>['rows'=>3]])

{{-- @include($helper->bladePath('includes.form-input','b'), ['type'=>'text', 'name' =>
"page_id"]) --}}

<input type="hidden" name="return_to_the_list" value="">
