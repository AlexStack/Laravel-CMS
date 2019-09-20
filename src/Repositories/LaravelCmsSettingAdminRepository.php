<?php

namespace AlexStack\LaravelCms\Repositories;

use AlexStack\LaravelCms\Models\LaravelCmsSetting;

class LaravelCmsSettingAdminRepository extends BaseRepository
{
    /**
     * Configure the Model.
     **/
    public function model()
    {
        return LaravelCmsSetting::class;
    }

    /**
     * Controller methods.
     */
    public function index()
    {
        $data['settings'] = LaravelCmsSetting::orderBy('sort_value', 'desc')->orderBy('id', 'desc')->get();

        if (empty($this->helper->settings)) {
            $this->helper->rewriteConfigFile(); // create settings file
            $this->helper = new LaravelCmsHelper(); // reload new settings
        }

        $data['helper']     = $this->helper;
        $data['categories'] = $this->getCategories($data['settings'], true);

        return $data;
    }

    public function create()
    {
        $data['helper']     = $this->helper;
        $data['categories'] = $this->getCategories(null, false);

        return $data;
    }

    public function store($form_data)
    {
        $must_json = 'plugins' == $form_data['category'];
        if (! $this->helper->correctJsonFormat($form_data['param_value'], $must_json)) {
            exit(sprintf($this->helper->t('wrong_json_format_str', ['name' => 'Param Value'])));
        }

        $must_json = '' != trim($form_data['input_attribute']);
        if (! $this->helper->correctJsonFormat($form_data['input_attribute'], $must_json)) {
            exit(sprintf($this->helper->t('wrong_json_format_str', ['name' => 'Input Attribute'])));
        }

        $rs = new LaravelCmsSetting();
        foreach ($rs->fillable as $field) {
            if (isset($form_data[$field])) {
                $rs->$field = trim($form_data[$field]);
            }
        }
        $rs->save();

        $this->helper->rewriteConfigFile();

        return $rs;
    }

    public function update($form_data, $id)
    {
        $form_data['id'] = $id;
        $setting         = LaravelCmsSetting::find($form_data['id']);

        unset($form_data['_method'], $form_data['_token']);

        $must_json = 'plugins' == $form_data['category'];
        if (! $this->helper->correctJsonFormat($form_data['param_value'], $must_json)) {
            exit(sprintf($this->helper->t('wrong_json_format_str', ['name' => 'Param Value'])));
        }
        $must_json = '' != trim($form_data['input_attribute']);
        if (! $this->helper->correctJsonFormat($form_data['input_attribute'], $must_json)) {
            exit(sprintf($this->helper->t('wrong_json_format_str', ['name' => 'Input Attribute'])));
        }

        if ('backend_language' == $form_data['param_name'] && $setting->param_value != $form_data['param_value']) {
            $need_update_config_file_twice = true;
        }

        $setting->update($form_data);

        $this->helper->rewriteConfigFile();

        if (isset($need_update_config_file_twice)) {
            $this->helper = new \AlexStack\LaravelCms\Helpers\LaravelCmsHelper(); // reload new settings
            $this->helper->rewriteConfigFile(); // replace language variables
        }

        return $setting;
    }

    public function edit($id)
    {
        $data['setting']    = LaravelCmsSetting::find($id);
        $data['helper']     = $this->helper;
        $data['categories'] = $this->getCategories(null, false);

        if ('template' == $data['setting']->category && 'frontend_dir' == $data['setting']->param_name) {
            $data['setting']->input_attribute = $this->getCmsTemplates()['frontend_attributes'];
        } elseif ('template' == $data['setting']->category && 'backend_dir' == $data['setting']->param_name) {
            $data['setting']->input_attribute = $this->getCmsTemplates()['backend_attributes'];
        }

        return $data;
    }

    public function destroy($id)
    {
        $rs = LaravelCmsSetting::find($id)->delete();

        return $rs;
    }

    /**
     * Other methods.
     */
    public function getCategories($settings = null, $allow_html = true)
    {
        if (! $settings) {
            $settings = LaravelCmsSetting::groupBy('category')->get(['category', 'category']);
            //$settings = LaravelCmsSetting::orderBy('sort_value', 'desc')->orderBy('id', 'desc')->get();
        }
        //$this->helper->debug($settings);
        $custom_cats = $this->helper->s('category.admin_setting_tabs');
        if (! $custom_cats) {
            $custom_cats = [];
        }
        $all_cats = $settings->pluck('category', 'category')->toArray();
        $new_cats = array_merge($custom_cats, $all_cats);
        array_walk($new_cats, function (&$item, $key) use ($custom_cats, $allow_html) {
            if (isset($custom_cats[$key])) {
                $item = $custom_cats[$key];
            } else {
                $item = $this->helper->t($item);
            }
            if (! $allow_html) {
                $item = strip_tags($item);
            }
        });

        return $new_cats;
    }

    public function getCmsTemplates()
    {
        $app_view_dir = base_path('resources/views/vendor/laravel-cms');

        if (! file_exists($app_view_dir)) {
            $app_view_dir = dirname(__FILE__, 2).'/resources/views';
        }
        $files = glob($app_view_dir.'/*', GLOB_ONLYDIR);

        //$this->helper->debug($files);

        foreach ($files as $dir) {
            if (strpos($dir, '-bak')) {
                continue;
            }

            if (file_exists($dir.'/config.php')) {
                $config_ary   = [];
                $template_ary = [];
                $config_ary   = include $dir.'/config.php';
                $backend_lang = $this->helper->s('template.backend_language');

                if (isset($config_ary[$backend_lang]['blade_files'])) {
                    $template_ary = $config_ary[$backend_lang];
                } elseif (isset($config_ary['en']['blade_files'])) {
                    $template_ary = $config_ary['en'];
                } elseif (isset($config_ary['blade_files'])) {
                    $template_ary = $config_ary;
                    // $option_ary = $config_ary['blade_files'] + $option_ary;
                }
                if (isset($template_ary['theme_name'])) {
                    $dirname = basename($dir);
                    if (false !== strpos($dir, 'backend')) {
                        $data['backend_templates'][$dirname] = $template_ary;
                        $data['backend_options'][$dirname]   = $template_ary['theme_name'];
                    } else {
                        $data['frontend_templates'][$dirname] = $template_ary;
                        $data['frontend_options'][$dirname]   = $template_ary['theme_name'];
                    }
                }
            }
        }

        $data['frontend_attributes']             = '{"select_options":'.json_encode($data['frontend_options']).',"rows":1,"required":"required"}';

        $data['backend_attributes']             = '{"select_options":'.json_encode($data['backend_options']).',"rows":1,"required":"required"}';

        // $this->helper->debug($data);

        return $data;
    }
}
