@include('laravel-cms::' . $helper->getCmsSetting('template_backend_dir') .  '.includes.form-input', ['type'=>'text', 'name' => "category","input_attributes"=>['required'=>'required','pattern'=>'[a-zA-Z0-9\-_]{2,60}']])

@include('laravel-cms::' . $helper->getCmsSetting('template_backend_dir') .  '.includes.form-input', ['type'=>'text', 'name' => "param_name","input_attributes"=>['required'=>'required','pattern'=>'[a-zA-Z0-9\-_]{2,60}']])

@php
    $attr = isset($setting) ? json_decode($setting->input_attribute, TRUE) : [];
    //$helper->debug($attr);
    $input_type = ( isset($attr['rows']) && $attr['rows'] > 1 ) ?'textarea' : 'text';
    if ( isset($attr['select_options']) && is_array($attr['select_options']) )   {
        $input_type     = 'select';
        $select_options = $attr['select_options'];
        unset($attr['select_options']);
    } else {
        $select_options = [];
    }
@endphp

@include('laravel-cms::' . $helper->getCmsSetting('template_backend_dir') .  '.includes.form-input', [
    'type'              => $input_type,
    'name'              => "param_value",
    'options'           => $select_options,
    "input_attributes"  => $attr
])



@include('laravel-cms::' . $helper->getCmsSetting('template_backend_dir') .  '.includes.form-input', ['name' => "enabled", 'type'=>'select', 'label'=>'Enable', 'options'=>['1' => 'Enable', '0' => 'Disable'] ])

@include('laravel-cms::' . $helper->getCmsSetting('template_backend_dir') .  '.includes.form-input', ['type'=>'text', 'name' => "sort_value"])


@include('laravel-cms::' . $helper->getCmsSetting('template_backend_dir') .  '.includes.form-input', ['name' => "input_attribute", 'type'=>'textarea', 'label'=>'Input Attribute', "input_attributes"  =>['rows'=>4]])


@include('laravel-cms::' . $helper->getCmsSetting('template_backend_dir') .  '.includes.form-input', ['type'=>'textarea', 'name' => "abstract", "input_attributes"=>['rows'=>3]])

@include('laravel-cms::' . $helper->getCmsSetting('template_backend_dir') .  '.includes.form-input', ['type'=>'text', 'name' => "page_id"])

<input type="hidden" name="return_to_the_list" value="">
