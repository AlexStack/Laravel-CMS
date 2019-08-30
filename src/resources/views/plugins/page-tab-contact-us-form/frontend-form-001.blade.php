contact us form contact us form contact us form


@include('laravel-cms::' . config('laravel-cms.template_backend_dir') .  '.includes.form-input', ['name' => "firstname"])


@include('laravel-cms::' . config('laravel-cms.template_backend_dir') .  '.includes.form-input', ['name' => "lastname"])



@include('laravel-cms::' . config('laravel-cms.template_backend_dir') .  '.includes.form-input', ['type'=>'textarea', 'name' => "message"])
