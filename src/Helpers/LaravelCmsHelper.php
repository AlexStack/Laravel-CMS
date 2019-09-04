<?php

namespace AlexStack\LaravelCms\Helpers;

use AlexStack\LaravelCms\Models\LaravelCmsPage;

class LaravelCmsHelper
{

    static public function hasPermission()
    {
        // return true;
        $user = \Auth::user();
        if (!$user) {
            exit('Can not get user info. Please logout and re-login again ');
        }

        if (!in_array($user->id, config('laravel-cms.admin_id_ary'))) {
            exit('Access denied for user id ' . $user->id);
        }

        if (!isset($_COOKIE['user_id'])) {
            $expire_time = time() + 3600 * 24 * 180; // 180 days
            setcookie('user_id', $user->id, $expire_time, '/');
        }
        return $user;
    }

    static public function imageUrl($img_obj, $width = null, $height = null, $resize_type = 'ratio')
    {
        if (!isset($img_obj->id)) {
            return self::assetUrl('images/no-image.png', false);
        }
        if (!is_numeric($width)) {
            $width = null;
        }
        if (!is_numeric($height)) {
            $height = null;
        }


        if ($img_obj->suffix == 'svg' || ($width == null && $height == null)) {
            $original_img_url = '/storage/' . config('laravel-cms.upload_dir') . '/' . $img_obj->path;
            return $original_img_url;
        }

        if (config('laravel-cms.image_encode') == 'jpg') {
            $suffix = 'jpg';
        } else {
            $suffix = $img_obj->suffix;
        }

        $filename   = $img_obj->id . '_' . ($width ?? 'auto') . '_' . ($height ?? 'auto') . '_' . $resize_type . '.' . $suffix;

        $related_dir = 'storage/' . config('laravel-cms.upload_dir') . '/optimized/' . substr($img_obj->id, -2);

        $abs_real_dir = public_path($related_dir);
        $abs_real_path = $abs_real_dir . '/' . $filename;
        $web_url = '/' . $related_dir . '/' . $filename;

        if (file_exists($abs_real_path) && filemtime($abs_real_path) > time() - config('laravel-cms.image_reoptimize_time')) {
            return $web_url;
            //return $abs_real_path . ' - already exists - ' . $web_url;
        }

        if (!file_exists($abs_real_dir)) {
            mkdir($abs_real_dir, 0755, true);
        }

        $original_img = public_path('storage/' . config('laravel-cms.upload_dir') . '/' . $img_obj->path);

        //self::debug($original_img);

        // resize the image to a width of 800 and constrain aspect ratio (auto height)
        $new_img = \Intervention\Image\ImageManagerStatic::make($original_img)->orientate()->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        if ($suffix == 'jpg' || $suffix == 'jpeg') {
            $new_img->encode('jpg');
        }
        $new_img->save($abs_real_path, 75);

        return $web_url;
        // return $abs_real_path . ' optimized image created ' . $width;
    }


    static public function debug($data, $exit = 'exit')
    {
        if (is_a($data, 'Illuminate\Database\Eloquent\Collection')) {
            $data = $data->toArray();
        }
        echo '<pre>' . var_export($data, true) . '</pre>';
        echo '<hr>Debug Time: ' . date('Y-m-d H:i:s') . '<hr>';
        if ($exit != 'no_exit') {
            exit();
        }
    }

    static public function menus()
    {
        $data['menus'] = LaravelCmsPage::with('menus:title,menu_title,id,parent_id,slug,redirect_url,menu_enabled')
            ->whereNull('parent_id')
            ->where('menu_enabled', 1)
            ->orderBy('sort_value', 'desc')
            ->orderBy('id', 'desc')
            ->get(['title', 'menu_title', 'id', 'parent_id', 'slug', 'redirect_url', 'menu_enabled']);

        //var_dump($data['menus']->toArray());
        //$this->debug($data['menus']);

        return $data['menus'];
    }


    static public function url($page, $is_abs_link = false)
    {
        $slug_suffix = config('laravel-cms.slug_suffix');
        if (!$page->slug) {
            $page->slug = $page->id . config('laravel-cms.slug_suffix');
        }
        if (trim($page->redirect_url) != '') {
            return trim($page->redirect_url);
        }
        if ($page->slug == 'homepage' || $page->slug == 'homepage' . $slug_suffix) {
            return route('LaravelCmsPages.index', [], $is_abs_link);
        }
        return route('LaravelCmsPages.show', $page->slug, $is_abs_link);
    }


    static public function assetUrl($file, $with_modify_time = true, $is_backend = false)
    {
        $url = 'laravel-cms/' . config('laravel-cms.' . ($is_backend ? 'template_backend_dir' : 'template_frontend_dir')) . '/' . $file;
        if ($with_modify_time) {
            $abs_real_path = public_path($url);

            if (file_exists($abs_real_path)) {
                $url .= '?last_modify_time=' . date('Ymd-His', filemtime($abs_real_path));
            } else {
                $url .= '?file_not_exists_please_publish_it_first';
            }
        }
        return '/' . $url;
    }

    static public function activeMenuClass($menu_item, $current_page, $class = 'active')
    {
        $p = array_column($current_page->parent_flat_ary, 'id');

        if ($menu_item->menu_enabled && $menu_item->parent_id && !empty($p)) {
            $p = array_diff($p, [$menu_item->parent_id]);
        }
        if ($menu_item->id == $current_page->id || in_array($menu_item->id, $p)) {
            return $class;
        }
        return '';
    }

    static public function getPlugins($prefix = 'page-tab-')
    {
        $app_view_dir = base_path('resources/views/vendor/laravel-cms') . '/plugins';

        if (!file_exists($app_view_dir)) {
            $app_view_dir = dirname(__FILE__, 2) . '/resources/views/plugins';
        }
        $dirs = glob($app_view_dir . "/" . $prefix . "*");
        $option_ary = [];
        foreach ($dirs as $d) {
            if (file_exists($d . '/config.php')) {
                $config_ary = include($d . '/config.php');
                if (isset($config_ary['blade_file']) && file_exists($d . '/' . $config_ary['blade_file']  . '.blade.php') && $config_ary['enabled']) {
                    $config_ary['blade_dir'] = basename($d);
                    $option_ary[] = $config_ary;
                }
            }
        }
        return $option_ary;
    }



    static public function onetimeApiToken($temp_api_key = null)
    {
        if ($temp_api_key === null) {
            $temp_api_key = uniqid('laravel-cms-');
            $expire_time = time() + 600; // expire after 10 minutes, one time use
            setcookie('laravel_cms_temp_api_key', $temp_api_key, $expire_time, '/');
            //exit($temp_api_key);
        }
        if (strlen($temp_api_key) < 10 || !isset($_COOKIE['laravel_session']) || !isset($_COOKIE['user_id']) || !config('app.key')) {
            return false;
            //return 'wrong_parameters=' . $temp_api_key . '-' . $_COOKIE['user_id'] . '-' . $_COOKIE['laravel_session'] . '-' . config('app.key');
        }

        $token = hash('sha256', $temp_api_key . '-' . $_COOKIE['user_id'] . '-' . config('app.key'));

        return $token;
    }

    static public function verifyApiToken($onetime_token)
    {
        if (strlen($onetime_token) < 10 || !isset($_COOKIE['laravel_cms_temp_api_key']) || !isset($_COOKIE['laravel_session']) || !isset($_COOKIE['user_id'])) {
            return false;
            //return '-1=' . $_COOKIE['laravel_cms_temp_api_key'] . '-' . $_COOKIE['user_id'] . '-' . $_COOKIE['laravel_session'] . '-' . config('app.key');
        }
        $real_token = self::onetimeApiToken($_COOKIE['laravel_cms_temp_api_key']);
        if ($onetime_token != $real_token) {
            return false;
            //return '-2=' . $real_token . '----' . $_COOKIE['laravel_cms_temp_api_key'] . '-' . $_COOKIE['user_id'] . '-' . $_COOKIE['laravel_session'] . '-' . config('app.key');
        }
        if (!in_array($_COOKIE['user_id'], config('laravel-cms.admin_id_ary'))) {
            return false;
        }

        $expire_time = time() - 1; // expire now, one time use
        setcookie('laravel_cms_temp_api_key', '', $expire_time, '/');
        return true;
    }
}
