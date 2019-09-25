<!-- Nav tabs -->
<ul class="nav nav-tabs mb-2" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#main-content" role="tab"><i
                class="fas fa-cube mr-1"></i>{{ $helper->t('main_content') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#settings" role="tab"><i
                class="fas fa-cog mr-1"></i>{{ $helper->t('settings') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#extra-content" role="tab"><i
                class="fas fa-cubes mr-1"></i>{{ $helper->t('extra_content') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#seo" role="tab"><i class="fas fa-globe mr-1"></i>SEO</a>
    </li>

    <li class="nav-item d-none">
        <a class="nav-link" data-toggle="tab" href="#files" role="tab">Files</a>
    </li>

    {{-- @if ( isset($page) )
    <li class="nav-item">
        <a class="nav-link" href="{{$helper->url($page)}}" target="_blank"><i class="fas fa-eye mr-1"></i>Preview</a>
    </li>
    @endif --}}

    @foreach ( $page_tab_blades as $tab )
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#{{$tab['blade_file']}}" role="tab">{!! $tab['tab_name'] !!}</a>
    </li>
    @endforeach
</ul>

<!-- Tab panes -->
<div class="tab-content">
    <div class="tab-pane active" id="main-content" role="tabpanel">

        @include($helper->bladePath('includes.form-input','b'), ['type'=>'text', 'name'
        => "title", 'label'=>isset($page)? $helper->t('title') . ' <a class="text-info ml-2 "
            href="' . $helper->url($page) . '" target="_blank" title="Preview"><i
                class="fas fa-external-link-square-alt"></i></a>': $helper->t('title'),
        'input_attributes'=>['required'=>'required', 'minlength'=>2, 'pattern'=>".{2,}"]])

        @include($helper->bladePath('includes.form-input','b'), ['name' =>
        "menu_title"])

        @include($helper->bladePath('includes.form-input','b'), ['type'=>'file', 'name'
        => "main_banner", 'input_attributes'=>['class'=>'form-control input-main_banner mb-3','accept'=>'image/*'] ])

        @include($helper->bladePath('includes.form-input','b'), ['type'=>'textarea',
        'name' => "main_content"])

        @include($helper->bladePath('includes.form-input','b'), ['type'=>'file', 'name'
        => "main_image", 'input_attributes'=>['class'=>'form-control input-main_image mb-3','accept'=>'image/*'] ])


        @include($helper->bladePath('includes.form-input','b'), ['type'=>'textarea',
        'name' => "sub_content"])

    </div>


    <div class="tab-pane" id="extra-content" role="tabpanel">
        @include($helper->bladePath('includes.form-input','b'), ['type'=>'textarea',
        'name' => "abstract"])

        @include($helper->bladePath('includes.form-input','b'), ['name' =>
        "extra_text_1"])

        @include($helper->bladePath('includes.form-input','b'), ['type'=>'textarea',
        'name' => "extra_content_1"])

        @include($helper->bladePath('includes.form-input','b'), ['type'=>'file', 'name'
        => "extra_image_1", 'input_attributes'=>['class'=>'form-control input-extra_image_1 mb-3'] ])

        @include($helper->bladePath('includes.form-input','b'), ['name' =>
        "extra_text_2"])

        @include($helper->bladePath('includes.form-input','b'), ['type'=>'textarea',
        'name' => "extra_content_2"])

        @include($helper->bladePath('includes.form-input','b'), ['type'=>'file', 'name'
        => "extra_image_2", 'input_attributes'=>['class'=>'form-control input-extra_image_2 mb-3'] ])

        @include($helper->bladePath('includes.form-input','b'), ['name' =>
        "extra_text_3"])

        @include($helper->bladePath('includes.form-input','b'), ['type'=>'textarea',
        'name' => "extra_content_3"])

        @include($helper->bladePath('includes.form-input','b'), ['type'=>'file', 'name'
        => "extra_image_3", 'input_attributes'=>['class'=>'form-control input-extra_image_3 mb-3'] ])

        @include($helper->bladePath('includes.form-input','b'), ['type'=>'textarea',
        'name' => "special_text"])
    </div>
    <div class="tab-pane" id="seo" role="tabpanel">
        @include($helper->bladePath('includes.form-input','b'), ['name' => "slug",
        'input_attributes'=>(isset($page->slug) && $page->slug =='homepage')? ['readonly'=>'readonly'] : [] ] )
        @include($helper->bladePath('includes.form-input','b'), ['name' => "meta_title"
        ])
        @include($helper->bladePath('includes.form-input','b'), ['name' =>
        "meta_keywords"])
        @include($helper->bladePath('includes.form-input','b'), ['name' =>
        "meta_description"])
        @include($helper->bladePath('includes.form-input','b'), ['name' => "tags" ])
    </div>

    <div class="tab-pane" id="settings" role="tabpanel">
        @include($helper->bladePath('includes.form-input','b'), ['name' => "parent_id",
        'type'=>'select', 'label'=>$helper->t('parent_page'), 'options'=>$parent_page_options])

        @include($helper->bladePath('includes.form-input','b'), ['name' =>
        "menu_enabled", 'type'=>'select', 'options'=>['1' => 'Display In Menu', '0' => 'Hide In Menu'] ])

        @include($helper->bladePath('includes.form-input','b'), ['name' => "status",
        'type'=>'select', 'options'=>['publish' => 'Publish', 'pending' => 'Pending'] ])

        @include($helper->bladePath('includes.form-input','b'), ['name' =>
        "redirect_url" ])

        @include($helper->bladePath('includes.form-input','b'), ['name' =>
        "template_file", 'type'=>'select', 'options'=>$template_file_options])

        @include($helper->bladePath('includes.form-input','b'), ['name' =>
        "sort_value",'type'=>'number' ])

        @include($helper->bladePath('includes.form-input','b'), ['name' =>
        "view_counts",'type'=>'number' ])

        @include($helper->bladePath('includes.form-input','b'), ['name' =>
        "created_at",'type'=>'text' ])

        @include($helper->bladePath('includes.form-input','b'), ['name' =>
        "user_id",'type'=>'number' ])

    </div>
    <div class="tab-pane" id="files" role="tabpanel">Files</div>

    @foreach ( $page_tab_blades as $tab )
    <div class="tab-pane" id="{{$tab['blade_file']}}" role="tabpanel">
        @include( $helper->bladePath($tab['blade_dir'] . '.' . $tab['blade_file'], 'plugins'), ['tab_data'=>
        ($plugins[$tab['blade_file']] ?? null) ] )
    </div>
    @endforeach

</div>

<input type="hidden" name="return_to_the_list" value="">
