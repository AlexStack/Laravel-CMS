contact us form contact us form contact us form
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

@include('laravel-cms::' . config('laravel-cms.template_backend_dir') .  '.includes.form-input', ['name' => "display_form_fields"])

@include('laravel-cms::' . config('laravel-cms.template_backend_dir') .  '.includes.form-input', ['name' => "success_title"])

@include('laravel-cms::' . config('laravel-cms.template_backend_dir') .  '.includes.form-input', ['type'=>'textarea', 'name' => "success_content"])
