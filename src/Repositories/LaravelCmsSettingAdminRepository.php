<?php

namespace AlexStack\LaravelCms\Repositories;

use AlexStack\LaravelCms\Models\LaravelCmsSetting;
use AlexStack\LaravelCms\Repositories\BaseRepository;

class LaravelCmsSettingAdminRepository extends BaseRepository
{

    /**
     * Configure the Model
     **/
    public function model()
    {
        return LaravelCmsSetting::class;
    }

    /**
     * Controller methods
     */

    public function index()
    {

        $data['settings'] = LaravelCmsSetting::orderBy('sort_value', 'desc')->orderBy('id', 'desc')->get();

        if (empty($this->helper->settings)) {
            $this->helper->rewriteConfigFile(); // create settings file
            $this->helper = new LaravelCmsHelper; // reload new settings
        }

        $data['helper'] = $this->helper;

        $data['categories'] = $this->getCategories($data['settings'], true);

        return $data;
    }

    public function create()
    {
        $data['helper'] = $this->helper;

        $data['categories'] = $this->getCategories(null, false);

        return $data;
    }


    public function store($form_data)
    {

        $must_json = $form_data['category'] == 'plugins';
        if (!$this->helper->correctJsonFormat($form_data['param_value'], $must_json)) {
            exit(sprintf($this->helper->t('wrong_json_format_str', ['name' => 'Param Value'])));
        }

        $must_json = trim($form_data['input_attribute']) != '';
        if (!$this->helper->correctJsonFormat($form_data['input_attribute'], $must_json)) {
            exit(sprintf($this->helper->t('wrong_json_format_str', ['name' => 'Input Attribute'])));
        }

        $rs = new LaravelCmsSetting;
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
        $setting = LaravelCmsSetting::find($form_data['id']);

        unset($form_data['_method']);
        unset($form_data['_token']);

        $must_json = $form_data['category'] == 'plugins';
        if (!$this->helper->correctJsonFormat($form_data['param_value'], $must_json)) {
            exit(sprintf($this->helper->t('wrong_json_format_str', ['name' => 'Param Value'])));
        }
        $must_json = trim($form_data['input_attribute']) != '';
        if (!$this->helper->correctJsonFormat($form_data['input_attribute'], $must_json)) {
            exit(sprintf($this->helper->t('wrong_json_format_str', ['name' => 'Input Attribute'])));
        }

        if ($form_data['param_name'] == 'backend_language' && $setting->param_value != $form_data['param_value']) {
            $need_update_config_file_twice = true;
        }

        $setting->update($form_data);

        $this->helper->rewriteConfigFile();

        if (isset($need_update_config_file_twice)) {
            $this->helper = new LaravelCmsHelper; // reload new settings
            $this->helper->rewriteConfigFile(); // replace language variables
        }

        return $setting;
    }


    public function edit($id)
    {
        $data['setting'] = LaravelCmsSetting::find($id);

        $data['helper'] = $this->helper;

        $data['categories'] = $this->getCategories(null, false);

        return $data;
    }


    public function destroy($id)
    {

        $rs = LaravelCmsSetting::find($id)->delete();

        return $rs;
    }




    /**
     * Other methods
     */


    public function getCategories($settings = null, $allow_html = true)
    {
        if (!$settings) {
            $settings = LaravelCmsSetting::groupBy('category')->get(['category', 'category']);
            //$settings = LaravelCmsSetting::orderBy('sort_value', 'desc')->orderBy('id', 'desc')->get();
        }
        //$this->helper->debug($settings);
        $custom_cats =  $this->helper->s('category.admin_setting_tabs');
        if (!$custom_cats) {
            $custom_cats = [];
        }
        $all_cats = $settings->pluck('category', 'category')->toArray();
        $new_cats = array_merge($custom_cats, $all_cats);
        array_walk($new_cats, function (&$item, $key) use ($custom_cats, $allow_html) {
            if (isset($custom_cats[$key])) {
                $item =  $custom_cats[$key];
            } else {
                $item = $this->helper->t($item);
            }
            if (!$allow_html) {
                $item = strip_tags($item);
            }
        });
        return $new_cats;
    }
}
