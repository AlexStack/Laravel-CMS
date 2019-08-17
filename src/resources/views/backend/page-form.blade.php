<!-- Nav tabs -->
<ul class="nav nav-tabs mb-2" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#main-content" role="tab"><i class="fas fa-cube mr-1"></i>Main Content</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#settings" role="tab"><i class="fas fa-cog mr-1"></i>Settings</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#extra-content" role="tab"><i class="fas fa-cubes mr-1"></i>Extra Content</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#seo" role="tab"><i class="fas fa-globe mr-1"></i>SEO</a>
    </li>

    <li class="nav-item d-none">
        <a class="nav-link" data-toggle="tab" href="#files" role="tab">Files</a>
    </li>

    @if ( isset($page_model) )
    <li class="nav-item">
        <a class="nav-link" href="{{$controller->url($page_model)}}" target="_blank"><i class="fas fa-eye mr-1"></i>Preview</a>
    </li>
    @endif
</ul>

<!-- Tab panes -->
<div class="tab-content">
    <div class="tab-pane active" id="main-content" role="tabpanel">


        @include('laravel-cms::backend.form-input', ['type'=>'text', 'name' => "title"])

        @include('laravel-cms::backend.form-input', ['name' => "menu_title"])

        @include('laravel-cms::backend.form-input', ['name' => "main_text"])

        @include('laravel-cms::backend.form-input', ['type'=>'textarea', 'name' => "main_content"])

        @include('laravel-cms::backend.form-input', ['type'=>'file', 'name' => "main_image", 'input_attributes'=>['class'=>'form-control input-main_image mb-3'] ])

        @include('laravel-cms::backend.form-input', ['type'=>'file', 'name' => "main_banner", 'input_attributes'=>['class'=>'form-control input-main_image mb-3'] ])

    </div>


    <div class="tab-pane" id="extra-content" role="tabpanel">
        @include('laravel-cms::backend.form-input', ['type'=>'textarea', 'name' => "abstract"])

        @include('laravel-cms::backend.form-input', ['name' => "extra_text"])

        @include('laravel-cms::backend.form-input', ['type'=>'textarea', 'name' => "extra_content"])

        @include('laravel-cms::backend.form-input', ['type'=>'file', 'name' => "extra_image", 'input_attributes'=>['class'=>'form-control input-main_image mb-3'] ])

        @include('laravel-cms::backend.form-input', ['name' => "extra_text_2"])

        @include('laravel-cms::backend.form-input', ['type'=>'textarea', 'name' => "extra_content_2"])

        @include('laravel-cms::backend.form-input', ['type'=>'file', 'name' => "extra_image_2", 'input_attributes'=>['class'=>'form-control input-main_image mb-3'] ])

    </div>
    <div class="tab-pane" id="seo" role="tabpanel">
        @include('laravel-cms::backend.form-input', ['name' => "slug",'label'=>'Page URL(Filename/Slug)'])
        @include('laravel-cms::backend.form-input', ['name' => "meta_keywords"])
        @include('laravel-cms::backend.form-input', ['name' => "meta_description"])
        @include('laravel-cms::backend.form-input', ['name' => "tags",'label'=>'Multiple tags separate by comma ,'])
    </div>

    <div class="tab-pane" id="settings" role="tabpanel">
            @include('laravel-cms::backend.form-input', ['name' => "parent_id", 'type'=>'select', 'label'=>'Parent Page', 'options'=>$parent_page_options])

            @include('laravel-cms::backend.form-input', ['name' => "menu_enabled", 'type'=>'select', 'label'=>'Menu Enabled', 'options'=>['1' => 'Display In Menu', '0' => 'Hide In Menu'] ])

            @include('laravel-cms::backend.form-input', ['name' => "status", 'type'=>'select', 'label'=>'Status', 'options'=>['publish' => 'Publish', 'pending' => 'Pending'] ])

            @include('laravel-cms::backend.form-input', ['name' => "redirect_url" ])

            @include('laravel-cms::backend.form-input', ['name' => "template_file",'label'=>'Blade Template File'])

            @include('laravel-cms::backend.form-input', ['name' => "sort_value",'type'=>'number', 'label'=>'Sort Value'])

            @include('laravel-cms::backend.form-input', ['name' => "view_counts",'type'=>'number' ])

            @include('laravel-cms::backend.form-input', ['name' => "created_at",'type'=>'text' ])

            @include('laravel-cms::backend.form-input', ['name' => "user_id",'type'=>'number' ])

    </div>
    <div class="tab-pane" id="files" role="tabpanel">Files</div>

</div>
