@include('laravel-cms::' . $helper->getCmsSetting('template_backend_dir') .  '.includes.form-input', ['type'=>'text', 'name' => "category"])

@include('laravel-cms::' . $helper->getCmsSetting('template_backend_dir') .  '.includes.form-input', ['type'=>'text', 'name' => "param_name"])

@include('laravel-cms::' . $helper->getCmsSetting('template_backend_dir') .  '.includes.form-input', ['type'=>'textarea', 'name' => "param_value", "input_attributes"=>['rows'=>5]])


@include('laravel-cms::' . $helper->getCmsSetting('template_backend_dir') .  '.includes.form-input', ['name' => "enabled", 'type'=>'select', 'label'=>'Enable', 'options'=>['1' => 'Enable', '0' => 'Disable'] ])

@include('laravel-cms::' . $helper->getCmsSetting('template_backend_dir') .  '.includes.form-input', ['name' => "input_type", 'type'=>'select', 'label'=>'Input Type', 'options'=>['text' => 'One line Text input', 'textarea' => 'Multiple line Text input'] ])


@include('laravel-cms::' . $helper->getCmsSetting('template_backend_dir') .  '.includes.form-input', ['type'=>'textarea', 'name' => "abstract", "input_attributes"=>['rows'=>3]])

@include('laravel-cms::' . $helper->getCmsSetting('template_backend_dir') .  '.includes.form-input', ['type'=>'text', 'name' => "page_id"])
