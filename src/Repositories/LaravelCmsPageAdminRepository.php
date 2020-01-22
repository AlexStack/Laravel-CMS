<?php

namespace AlexStack\LaravelCms\Repositories;

use AlexStack\LaravelCms\Models\LaravelCmsFile;
use AlexStack\LaravelCms\Models\LaravelCmsPage;

class LaravelCmsPageAdminRepository extends BaseRepository
{
    /**
     * Configure the Model.
     **/
    public function model()
    {
        return LaravelCmsPage::class;
    }

    /**
     * Controller methods.
     */
    public function index()
    {
        //$data['all_pages'] = LaravelCmsPage::orderBy('id','desc')->get();

        $all_page_ary = $this->flattenArray($this->parentPages('all')->toArray(), 'children4list');

        $data['all_pages']  = json_decode(json_encode($all_page_ary), false);

        $data['helper'] = $this->helper;

        return $data;
    }

    public function create()
    {
        $data['parent_page_options']   = $this->parentPages();
        $data['template_file_options'] = $this->templateFileOption();
        $data['helper']                = $this->helper;
        $data['page_tab_blades']       = $this->extraPageTabs();

        $data['plugins'] = $this->extraPageTabs('create');

        return $data;
    }

    public function store($form_data)
    {
        $all_file_data = [];
        $this->handleUpload($form_data, $all_file_data);
        //$this->helper->debug($form_data, 'no_exit');

        $form_data['slug'] = $this->getSlug($form_data);

        // tags string to json
        $form_data['tags'] = $this->commaStrToJson($form_data['tags']);

        // DB::enableQueryLog();

        // $rs = LaravelCmsPage::create($form_data);  // create() not working ???

        // var_dump($form_data);
        // exit();

        $rs = new LaravelCmsPage();
        foreach ($rs->fillable as $field) {
            if (isset($form_data[$field])) {
                $rs->$field = trim($form_data[$field]);
            }
        }
        $rs->save();
        //$this->helper->debug($rs);

        // $sql = DB::getQueryLog();
        // $this->helper->debug($sql);
        if (null == $rs->slug || '' == trim($rs->slug)) {
            $rs->save(['slug' => $this->generateSlug('', $rs->id)]);
        }

        $this->extraPageTabs('store', $form_data, $rs);

        return $rs;
    }

    public function update($form_data, $id)
    {
        $form_data['id'] = $id;

        $page = LaravelCmsPage::find($form_data['id']);
        // if (!$page->user_id && isset($this->user->id)) {
        //     $form_data['user_id'] = $this->user->id;
        // }

        if ('' != trim($form_data['special_text']) && ! $this->helper->correctJsonFormat($form_data['special_text'])) {
            exit(sprintf($this->helper->t('wrong_json_format_str', ['name' => 'Param Value'])));
        }

        $form_data['slug'] = $this->getSlug($form_data);

        $all_file_data = json_decode($page->file_data, true); // json2array

        //$this->debug($all_file_data, 'exit');
        $this->handleUpload($form_data, $all_file_data);

        // tags string to json
        $form_data['tags'] = $this->commaStrToJson($form_data['tags']);

        unset($form_data['_method'], $form_data['_token']);

        $page->update($form_data);

        $this->extraPageTabs('update', $form_data, $page);

        return $page;
    }

    public function edit($id)
    {
        $data['page_tab_blades'] = $this->extraPageTabs();

        $data['page'] = LaravelCmsPage::find($id);
        //$data['parent_page_options'] = array_merge(array(null=>"Top Level"),  $this->parentPages()->pluck('title', 'id')->toArray());

        $data['parent_page_options']   = $this->parentPages();
        $data['template_file_options'] = $this->templateFileOption();

        $data['file_data'] = json_decode($data['page']->file_data);
        if (null == $data['file_data']) {
            $data['file_data'] = json_decode('{}');
        }

        // $this->debug($data['file_data'], 'exit');

        $data['file_data']->file_dir = asset(''.$this->helper->s('file.upload_dir'));

        $data['helper'] = $this->helper;

        $data['plugins'] = $this->extraPageTabs('edit', $id, $data['page']);

        // tags
        if ($tags_array = json_decode($data['page']->tags, true)) {
            $data['page']->tags = implode(' , ', $tags_array);
        }

        //$this->helper->debug($data['plugins']->toArray(), 'no_exit22');

        return $data;
    }

    public function destroy($id)
    {
        $page = LaravelCmsPage::find($id);

        $this->extraPageTabs('destroy', $id, $page);

        $rs = $page->delete();

        return $rs;
    }

    /**
     * Other methods.
     */
    private function handleUpload(&$form_data, &$all_file_data = [])
    {
        $request  = request();
        $file_ary = ['main_image', 'main_banner', 'extra_image_1', 'extra_image_2', 'extra_image_3'];
        foreach ($file_ary as $field_name) {
            $field_name_delete    = $field_name.'_delete';
            $field_name_hidden_id = $field_name.'_id';
            if ($request->$field_name_hidden_id) {
                // file already exists
                $all_file_data[$field_name] = LaravelCmsFile::find($request->$field_name_hidden_id)->toArray();
                $form_data[$field_name]     = $request->$field_name_hidden_id;
            } elseif ($request->hasFile($field_name)) {
                // upload file
                $all_file_data[$field_name] = $this->helper->uploadFile($request->file($field_name))->toArray();
                $form_data[$field_name]     = $all_file_data[$field_name]['id'];
            } elseif ($request->$field_name_delete) {
                $all_file_data[$field_name] = null;
                $form_data[$field_name]     = null;
            }
        }
        $form_data['file_data'] = json_encode($all_file_data);

        return $form_data;
    }

    public function generateSlug($slug, $def = null, $separate = '-')
    {
        $slug_format = $this->helper->s('system.slug_format');
        $slug_suffix = $this->helper->s('system.slug_suffix');
        $separate    = $this->helper->s('system.slug_separate') ?? $separate;

        if ('zh' == $this->helper->s('template.frontend_language')) {
            if ('from_title' == $slug_format) {
                $slug_format    = 'pinyin';
            }
            $normalizeChars = [];
        } else {
            $normalizeChars = [
                'Š' => 'S', 'š' => 's', 'Ð' => 'Dj', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A',
                'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I',
                'Ï' => 'I', 'Ñ' => 'N', 'Ń' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U',
                'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
                'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i',
                'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ń' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u',
                'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ý' => 'y', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', 'ƒ' => 'f',
                'ă' => 'a', 'î' => 'i', 'â' => 'a', 'ș' => 's', 'ț' => 't', 'Ă' => 'A', 'Î' => 'I', 'Â' => 'A', 'Ș' => 'S', 'Ț' => 'T',
            ];
        }

        if ($slug || '' != trim($slug)) {
            $slug = preg_replace('/[^A-Za-z0-9-\._]+/', $separate, trim(strtr($slug, $normalizeChars)));
            if (false === strpos($slug, '.') && false !== strpos($slug_suffix, '.') && 'homepage' != $slug) {
                return $slug.$slug_suffix;
            }

            return $slug;
        } elseif ('pinyin' == $slug_format) {
            $pinyin = new \Overtrue\Pinyin\Pinyin('\Overtrue\Pinyin\GeneratorFileDictLoader');
            $slug   = strtr($pinyin->permalink(trim($def), '-'), $normalizeChars);

            $slug = ucwords($slug, '-');
            //exit($slug); // some Chinese can not convert
            $slug = preg_replace('/[^A-Za-z0-9-\._]+/', $separate, $slug);

            if ('-' != $separate) {
                $slug = str_replace('-', $separate, $slug);
            }
        } elseif ('from_title' == $slug_format) {
            $slug = preg_replace('/[^A-Za-z0-9-\._]+/', $separate, trim(strtr($def, $normalizeChars)));
        } else {
            if (! $def) {
                return '';
            }
            $slug = trim($def);
        }
        if (strlen($slug) > (190 - strlen($slug_suffix))) {
            $slug = substr($slug, 0, (190 - strlen($slug_suffix)));
        }

        return $slug.$slug_suffix;
    }

    public function getSlug($form_data)
    {
        $slug_format = $this->helper->s('system.slug_format');
        $slug_suffix = $this->helper->s('system.slug_suffix');

        $default_slug = 'id' == $slug_format ? $form_data['id'] : ($form_data['menu_title'] ?? $form_data['title']);
        $new_slug     = $this->generateSlug($form_data['slug'], $default_slug);
        $rs           = LaravelCmsPage::where('slug', $new_slug)->first();
        if (isset($rs->id) && isset($form_data['id']) && $rs->id != $form_data['id']) {
            if ('' != $slug_suffix) {
                $new_slug = str_replace($slug_suffix, '', $new_slug).'-'.uniqid().$slug_suffix;
            } else {
                $new_slug .= '-'.uniqid();
            }
        }

        return $new_slug;
    }

    public function flattenArray($elements, $name = 'children', $depth = 0)
    {
        $result = [];

        foreach ($elements as $element) {
            $element['depth'] = $depth;

            if (isset($element[$name])) {
                $children = $element[$name];
                unset($element[$name]);
            } else {
                $children = null;
            }

            $result[] = $element;

            if (isset($children)) {
                $result = array_merge($result, $this->flattenArray($children, $name, $depth + 1));
            }
        }

        return $result;
    }

    public function parentPages($action = 'get_select_options')
    {
        $data['children'] = LaravelCmsPage::with('children4list:title,menu_title,id,parent_id,menu_enabled,slug,redirect_url,sort_value,status')
            ->whereNull('parent_id')
            ->orderBy('sort_value', 'desc')
            ->orderBy('id', 'desc')
            ->get(['title', 'menu_title', 'id', 'parent_id', 'menu_enabled', 'slug', 'redirect_url', 'sort_value', 'status']);

        //var_dump($data['children']->toArray());

        if ('get_select_options' == $action) {
            $options  = [null => $this->helper->t('top_level')];
            $flat_ary = $this->flattenArray($data['children']->toArray(), 'children4list');

            //var_dump($flat_ary);

            foreach ($flat_ary as $item) {
                $title                = $item['menu_title'] ?? $item['title'];
                $options[$item['id']] = '-'.str_repeat('--', $item['depth']).' '.$title;
            }

            return json_decode(json_encode($options), false); // return object
            //return $options;
        }

        return $data['children'];
    }

    public function extraPageTabs($action = 'return_options', $form_data = null, $page = null)
    {
        $option_ary = $this->helper->getPlugins('page-tab-');

        // $this->helper->debug($option_ary);

        if ('return_options' == $action) {
            return $option_ary;
        } elseif (in_array($action, ['create', 'edit', 'store', 'update', 'destroy'])) {
            $callback_ary = collect([]);
            foreach ($option_ary as $plugin) {
                $plugin_class = trim($plugin['php_class'] ?? '');
                if ('' != $plugin_class && class_exists($plugin_class) && is_callable($plugin_class.'::'.$action)) {
                    //echo $plugin_class . '::' . $action . '  --- ';

                    $s = call_user_func([new $plugin_class(), $action], $form_data, $page, $plugin);
                    $callback_ary->put($plugin['blade_file'], $s);

                // if ('sub-page' == $plugin['blade_file']) {
                    //     $this->helper->debug($s->toArray());
                    // }
                } else {
                    $callback_ary->put($plugin['blade_file'], null);
                }
            }
            //dd($callback_ary);

            return $callback_ary;
        }

        //$this->helper->debug($option_ary);
        return $option_ary;
    }

    public function templateFileOption()
    {
        $app_view_dir = base_path('resources/views/vendor/laravel-cms').'/'.$this->helper->s('template.frontend_dir');

        if (! file_exists($app_view_dir)) {
            $app_view_dir = dirname(__FILE__, 2).'/resources/views/'.$this->helper->s('template.frontend_dir');
        }
        $files = glob($app_view_dir.'/*.blade.php');

        //$this->helper->debug($files);
        foreach ($files as $f) {
            $k              = str_replace('.blade.php', '', basename($f));
            $option_ary[$k] = ucwords(str_replace(['-', '_', '.'], ' ', $k));
        }

        // override the real file names with config blade_files settings
        if (file_exists($app_view_dir.'/config.php')) {
            $config_ary   = [];
            $template_ary = [];
            $config_ary   = include $app_view_dir.'/config.php';
            $backend_lang = $this->helper->s('template.backend_language');

            if (isset($config_ary[$backend_lang]['blade_files'])) {
                $template_ary = $config_ary[$backend_lang];
            } elseif (isset($config_ary['en']['blade_files'])) {
                $template_ary = $config_ary['en'];
            } elseif (isset($config_ary['blade_files'])) {
                $template_ary = $config_ary;
            }

            if (isset($template_ary['blade_files']) && is_array($template_ary['blade_files'])) {
                $option_ary = $template_ary['blade_files'] + $option_ary;
            }
        }

        return $option_ary;
    }

    public function commaStrToJson($str)
    {
        if ('' != trim($str)) {
            $str        = str_replace(['，', ';', '|'], ',', $str);
            // trim every elements
            $tags_array = array_map('trim', explode(',', $str));
            // remove empty, duplicate and keys
            $tags_array = array_values(array_unique(array_filter($tags_array)));
            $str        = json_encode($tags_array, JSON_UNESCAPED_UNICODE);
        }

        return $str;
    }
}
