<?php

namespace AlexStack\LaravelCms\Helpers;

use AlexStack\LaravelCms\Models\LaravelCmsPage;
use AlexStack\LaravelCms\Models\LaravelCmsSetting;
use App;

class LaravelCmsHelper
{
    public $settings = [];
    public $user     = null;

    public function __construct()
    {
        $setting_file = storage_path('app/laravel-cms/settings.php');
        if (file_exists($setting_file)) {
            $this->settings = include $setting_file;
        }
        if (request()->is(substr(config('laravel-cms.admin_route'), 1).'/*')) {
            if (App::getLocale() != $this->s('template.backend_language')) {
                App::setLocale($this->s('template.backend_language'));
            }
        } else {
            if (App::getLocale() != $this->s('template.frontend_language')) {
                App::setLocale($this->s('template.frontend_language'));
            }
        }
        //echo App::getLocale() . ' - ' . config('laravel-cms.admin_route') . request()->url();
    }

    public function hasPermission($role = null)
    {
        // return true;
        $user = \Auth::user();
        if (! $user) {
            exit('Can not get user info. Please logout and re-login again ');
        }
        $cms_admin         = $this->s('system.cms_admin');
        $pre_defined_admin = config('laravel-cms.system.cms_admin');

        //$this->debug([$cms_admin, $pre_defined_admin]);
        if ($role && isset($pre_defined_admin[$role])) {
            exit('CMS admin role not allowed '.$role);
        }

        $user->laravel_cms_admin_role = null;
        foreach ($pre_defined_admin as $pre_defined_role => $admin_id_ary) {
            if (isset($cms_admin[$pre_defined_role]) && is_array($cms_admin[$pre_defined_role])) {
                if (in_array($user->id, $cms_admin[$pre_defined_role])) {
                    $user->laravel_cms_admin_role = $pre_defined_role;
                    break;
                } elseif ($role == $pre_defined_role) {
                    exit('Access denied for user id '.$user->id.' for admin role '.$role);
                }
            }
        }
        if (! $user->laravel_cms_admin_role) {
            exit('Access denied for user id '.$user->id);
        }

        if (! isset($_COOKIE['user_id'])) {
            $expire_time = time() + 3600 * 24 * 180; // 180 days
            setcookie('user_id', $user->id, $expire_time, '/');
            setcookie('laravel_cms_admin_role', $user->laravel_cms_admin_role, $expire_time, '/');
        }
        $this->user = $user;

        return $user;
    }

    public function getCmsSetting($param_name)
    {
        $val = false;
        if (false !== strpos($param_name, '.')) {
            $param_ary = explode('.', $param_name);
            $key_1     = $param_ary[0];
            $key_2     = $param_ary[1];
            if ('top' == $key_1) {
                return $this->settings[$key_2] ?? null;
            }
        } else {
            $key_1     = 'global';
            $key_2     = $param_name;
            $param_ary = [$param_name];
        }
        if (isset($this->settings[$key_1]) && isset($this->settings[$key_1][$key_2])) {
            if (isset($param_ary[2])) {
                $val =  $this->settings[$key_1][$key_2][$param_ary[2]] ?? false;
            } else {
                $val = $this->settings[$key_1][$key_2];
            }
        }

        if (false === $val) {
            $val = config('laravel-cms.'.$param_name);
        }

        return $val;
    }

    public function imageUrl($img_obj, $width = null, $height = null, $resize_type = 'ratio')
    {
        if (! isset($img_obj->id)) {
            return $this->assetUrl('images/no-image.png', false);
        }

        if (in_array($width, ['large', 'middle', 'small'])) {
            $height = $this->s('file.'.$width.'_image_height');
            $width  = $this->s('file.'.$width.'_image_width');
        }

        if (! is_numeric($width)) {
            $width = null;
        }
        if (! is_numeric($height)) {
            $height = null;
        }

        // $this->debug([$height , $width]);

        if ('svg' == $img_obj->suffix || (null == $width && null == $height) || ! isset($img_obj->is_image) || false == $img_obj->is_image) {
            // return original img url
            return '/'.$this->s('file.upload_dir').'/'.$img_obj->path;
        }

        if ('jpg' == $this->s('file.image_encode')) {
            $suffix = 'jpg';
        } else {
            $suffix = $img_obj->suffix;
        }

        $filename = $img_obj->id.'_'.($width ?? 'auto').'_'.($height ?? 'auto').'_'.$resize_type.'.'.$suffix;

        $related_dir = ''.$this->s('file.upload_dir').'/optimized/'.substr($img_obj->id, -2);

        $abs_real_dir  = public_path($related_dir);
        $abs_real_path = $abs_real_dir.'/'.$filename;
        $web_url       = '/'.$related_dir.'/'.$filename;

        if (file_exists($abs_real_path)) {
            $image_reoptimize_time = $this->s('image_reoptimize_time');
            if (0 == $image_reoptimize_time) {
                return $web_url;
            } elseif (filemtime($abs_real_path) > time() - $this->s('image_reoptimize_time')) {
                return $web_url;
            }
            //return $abs_real_path . ' - already exists - ' . $web_url;
        }

        if (! file_exists($abs_real_dir)) {
            mkdir($abs_real_dir, 0755, true);
        }

        $original_img = public_path(''.$this->s('file.upload_dir').'/'.$img_obj->path);

        if (! file_exists($original_img)) {
            return $web_url;
        }

        try {
            // resize the image to a width of 800 and constrain aspect ratio (auto height)
            $new_img = \Intervention\Image\ImageManagerStatic::make($original_img)->orientate()->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            if ('jpg' == $suffix || 'jpeg' == $suffix) {
                $new_img->encode('jpg');
            }
            $new_img->save($abs_real_path, 75);
        } catch (\Exception $e) {
            $web_url .= '?error='.$e->getMessage();
        }

        return $web_url;
        // return $abs_real_path . ' optimized image created ' . $width;
    }

    public static function debug($data, $exit = 'exit')
    {
        if (is_a($data, 'Illuminate\Database\Eloquent\Collection')) {
            $data = $data->toArray();
        }
        echo '<pre>'.var_export($data, true).'</pre>';
        echo '<hr>Debug Time: '.date('Y-m-d H:i:s').'<hr>';
        if ('no_exit' != $exit) {
            exit();
        }
    }

    public static function menus()
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

    public function url($page, $is_abs_link = false)
    {
        $slug_suffix = $this->s('system.slug_suffix');
        if (! $page->slug) {
            $page->slug = $page->id.$this->s('system.slug_suffix');
        }
        if ('' != trim($page->redirect_url)) {
            return trim($page->redirect_url);
        }
        if ('homepage' == $page->slug || $page->slug == 'homepage'.$slug_suffix) {
            $url =  route('LaravelCmsPages.index', [], $is_abs_link);
        }

        $url = route('LaravelCmsPages.show', $page->slug, $is_abs_link);

        if ($is_abs_link && 0 === strpos(config('app.url'), 'https://')) {
            $url = str_replace('http://', 'https://', $url);
        }

        return $url;
    }

    public function assetUrl($file, $with_modify_time = true, $is_backend = false)
    {
        $url = 'laravel-cms/'.$this->s(''.($is_backend ? 'template.backend_dir' : 'template.frontend_dir')).'/'.$file;
        if (0 === strpos($file, '../')) {
            $url = 'laravel-cms/'.str_replace('../', '', $file);
        }
        if ($with_modify_time) {
            $abs_real_path = public_path($url);

            if (file_exists($abs_real_path)) {
                $url .= '?last_modify_time='.date('Ymd-His', filemtime($abs_real_path));
            } else {
                $url .= '?file_not_exists=please_publish_it_first';
            }
        }

        return '/'.$url;
    }

    public function bladePath($file, $dir = 'frontend')
    {
        if ('frontend' == $dir || 'f' == $dir) {
            return 'laravel-cms::'.$this->s('template.frontend_dir').'.'.$file;
        } elseif ('backend' == $dir || 'b' == $dir) {
            return 'laravel-cms::'.$this->s('template.backend_dir').'.'.$file;
        } elseif ('plugins' == $dir || 'p' == $dir || 'plugin' == $dir) {
            return 'laravel-cms::plugins.'.$file;
        } elseif ('other' == $dir) {
            return 'laravel-cms::'.$file;
        } else {
            return 'laravel-cms::'.$dir.'.'.$file;
        }
    }

    public static function activeMenuClass($menu_item, $current_page, $class = 'active')
    {
        $p = array_column($current_page->parent_flat_ary, 'id');

        if ($menu_item->menu_enabled && $menu_item->parent_id && ! empty($p)) {
            $p = array_diff($p, [$menu_item->parent_id]);
        }
        if ($menu_item->id == $current_page->id || in_array($menu_item->id, $p)) {
            return $class;
        }

        return '';
    }

    public function getPlugins($prefix = null)
    {
        if (! isset($this->settings['plugin']) || ! is_array($this->settings['plugin'])) {
            //return $this->getPluginsFromFile($prefix);
            return [];
            //exit('no plugins in the settings');
        }

        $plugin_dir = base_path('resources/views/vendor/laravel-cms').'/plugins';

        if (! file_exists($plugin_dir)) {
            $plugin_dir = dirname(__FILE__, 2).'/resources/views/plugins';
        }
        $option_ary = [];
        foreach ($this->settings['plugin'] as $k => $config_ary) {
            if (null == $prefix || false !== strpos($k, $prefix)) {
                if (isset($config_ary['blade_file']) && file_exists($plugin_dir.'/'.$k.'/'.$config_ary['blade_file'].'.blade.php')) {
                    $config_ary['blade_dir'] = $k;
                    $option_ary[]            = $config_ary;
                }
            }
        }
        //$this->debug($option_ary);

        return $option_ary;
    }

    public static function getPluginsFromFile($prefix = 'page-tab-')
    {
        $app_view_dir = base_path('resources/views/vendor/laravel-cms').'/plugins';

        if (! file_exists($app_view_dir)) {
            $app_view_dir = dirname(__FILE__, 2).'/resources/views/plugins';
        }
        $dirs       = glob($app_view_dir.'/'.$prefix.'*');
        $option_ary = [];
        foreach ($dirs as $d) {
            if (file_exists($d.'/config.php')) {
                $config_ary = include $d.'/config.php';
                if (isset($config_ary['blade_file']) && file_exists($d.'/'.$config_ary['blade_file'].'.blade.php') && $config_ary['enabled']) {
                    $config_ary['blade_dir'] = basename($d);
                    $option_ary[]            = $config_ary;
                }
            }
        }

        return $option_ary;
    }

    // check json format in case some input should be json but made a mistake
    public static function correctJsonFormat($str, $must_json = false)
    {
        $str = trim($str);
        if ($must_json) {
            $json = json_decode($str);
            if (null === $json) {
                return false;
            }

            return true;
        } elseif ('{' == substr($str, 0, 1) && '}' == substr($str, -1)) {
            // the str should be json
            $json = json_decode($str);
            if (null === $json) {
                return false;
            }

            return true;
        }

        return 'not_json_format';
    }

    // alias of getCmsSetting() for short & clear code in template
    public function s($param_name)
    {
        return $this->getCmsSetting($param_name);
    }

    // Combine trans() & trans_choice() & set default
    public function t($key, $param_1 = null, $param_2 = null)
    {
        if (false !== strpos($key, ',')) {
            if (null == $param_1) {
                $separator = ' ';
                if (in_array(App::getLocale(), ['cn', 'zh'])) {
                    $separator = '';
                }
            } else {
                $separator = $param_1;
            }
            $s = [];
            foreach (explode(',', $key) as $k) {
                $s[] = $this->t(trim($k));
            }

            return implode($separator, $s);
        }

        $prefix = 'laravel-cms::';
        if (false === strpos($key, '.')) {
            $default_str = $key;
            $key         = 'cms.'.$key;
        }
        if (is_numeric($param_1)) {
            $s = is_array($param_2) ? trans_choice($prefix.$key, $param_1, $param_2) : trans_choice($prefix.$key, $param_1);
        } elseif (is_array($param_1)) {
            $s = __($prefix.$key, $param_1);
        } else {
            $s = __($prefix.$key);
        }
        if (false !== strpos($s, $prefix)) {
            if (! isset($default_str)) {
                $key_ary     = explode('.', $key);
                $default_str = end($key_ary);
            }

            $s = ucwords(str_replace(['-', '_'], ' ', $default_str));
        }

        return $s;
    }

    public static function fileIconCode($mimeType, $return = 'code')
    {
        if (preg_match('/xls|xlsx|xlsb|csv/i', $mimeType)) {
            $icon = 'excel';
            $code = '<i class="fas fa-file-excel"></i>';
        } elseif (preg_match('/doc|docx|docm|dotx|dotm|docb/i', $mimeType)) {
            $icon = 'word';
            $code = '<i class="fas fa-file-word"></i>';
        } elseif (preg_match('/ppt/i', $mimeType)) {
            $icon = 'powerpoint';
            $code = '<i class="fas fa-file-powerpoint"></i>';
        } elseif (preg_match('/txt|html|php|conf|java|asp/i', $mimeType)) {
            $icon = 'txt';
            $code = '<i class="fas fa-file-alt"></i>';
        } elseif (preg_match('/pdf/i', $mimeType)) {
            $icon = 'pdf';
            $code = '<i class="fas fa-file-pdf"></i>';
        } elseif (preg_match('/mp4|wmv|avi/i', $mimeType)) {
            $icon = 'video';
            $code = '<i class="fas fa-file-video"></i>';
        } elseif (preg_match('/png|jpg|jpeg|gif|bmp|svg/i', $mimeType)) {
            $icon = 'image';
            $code = '<i class="fas fa-file-image"></i>';
        } elseif (preg_match('/zip|rar|gz|tar|7z/i', $mimeType)) {
            $icon = 'zip';
            $code = '<i class="far fa-file-archive"></i>';
        } else {
            $icon = 'file';
            $code = '<i class="fas fa-file"></i>';
        }
        if ('code' != $return) {
            return 'icon-'.$icon;
        } else {
            return $code;
        }
    }

    public function uploadFile($f, $options = null)
    {
        if (is_string($f) && 0 === strpos(trim($f), 'http')) {
            $f         = trim($f);
            $file_path = public_path($this->s('file.upload_dir').'/temp_'.md5($f).'');
            $file_str  = @file_get_contents($f, false, $options);
            if (false === $file_str) {
                return false;
            }
            file_put_contents($file_path, $file_str);
            $file_data['url'] = $f;
            $f                = new \Illuminate\Http\UploadedFile($file_path, basename($f));
        }
        // $file_data['user_id'] = $user->id;
        $file_data['mimetype'] = $f->getMimeType();
        $file_data['suffix']   = $f->getClientOriginalExtension();
        $file_data['filename'] = $f->getClientOriginalName();
        $file_data['title']    = $file_data['filename'];
        $file_data['filesize'] = $f->getSize();
        if (false !== strpos($file_data['mimetype'], 'image/') && false === strpos($file_data['mimetype'], 'icon')) {
            $file_data['is_image'] = 1;
        }
        if (false !== strpos($file_data['mimetype'], 'video/')) {
            $file_data['is_video'] = 1;
        }
        $file_data['temp_path'] = method_exists($f, 'getRealPath') ? $f->getRealPath() : $f->path();

        $file_data['filehash'] = sha1_file($file_data['temp_path']);

        $file_data['path'] = substr($file_data['filehash'], -2).'/'.$file_data['filehash'].'.'.$file_data['suffix'];

        //$this->debug($file_data);

        $file_data['description'] = date('Y-m-d H:i:s'); // make some different otherwise the updated_at will not update
        $new_file                 = \AlexStack\LaravelCms\Models\LaravelCmsFile::updateOrCreate(
            ['filehash' => $file_data['filehash']],
            $file_data
        );

        //$f->storeAs(dirname('public/' . $this->helper->s('file.upload_dir') . '/' . $file_data['path']), basename($file_data['path']));

        $file_store_dir = public_path(dirname($this->s('file.upload_dir').'/'.$file_data['path']));

        // $this->debug($file_store_dir . '/'. basename($file_data['path']));

        // $f->move($file_store_dir, basename($file_data['path']));
        if (! file_exists($file_store_dir)) {
            mkdir($file_store_dir, 0755, true);
        }

        rename($file_data['temp_path'], $file_store_dir.'/'.basename($file_data['path']));

        return $new_file;

        // echo '<pre>111:' . var_export($new_file, true) . '</pre>';
        // exit();
    }

    /**
     * Replace __(str) to locale language string
     * Replace ROUTE(str) to real route() url.
     */
    public function parseCmsStr($str)
    {
        if (false !== strpos($str, '__(')) {
            $str = preg_replace_callback(
                "/__\((.*)\)/U",
                function ($matches) {
                    $s = trim(str_replace(['\\', '\'', '"'], '', $matches[1]));

                    return addslashes($this->t($s));
                },
                $str
            );
        }

        if (false !== strpos($str, 'SETTING(')) {
            $str = preg_replace_callback(
                "/SETTING\((.*)\)/U",
                function ($matches) {
                    $param_name = trim(str_replace(['\\', '\'', '"'], '', $matches[1]));

                    return $this->s($param_name) ?? 'no_setting_'.$matches[1];
                },
                $str
            );
        }

        if (false !== strpos($str, 'ROUTE(')) {
            $str = preg_replace_callback(
                "/ROUTE\((.*)\)/U",
                function ($matches) {
                    $route_name = trim(str_replace(['\\', '\'', '"'], '', $matches[1]));
                    if (strpos($route_name, ',')) {
                        $route_ary   = explode(',', $route_name);
                        $route_name  = trim($route_ary[0]);
                        $route_param = trim($route_ary[1]);
                    }
                    if (\Route::has($route_name)) {
                        return route($route_name, $route_param ?? [], false);
                    } else {
                        return '#route_not_defined_'.$matches[1];
                    }
                },
                $str
            );
        }

        return $str;
    }

    // store the settings as an array in a setting file to speed up
    public function rewriteConfigFile()
    {
        //sort by asc to override with high priority value
        $settings = LaravelCmsSetting::where('enabled', 1)
            ->orderBy('sort_value', 'asc')
            ->orderBy('id', 'asc')
            ->get(['param_name', 'param_value', 'category', 'page_id']);

        $config_ary = [];
        foreach ($settings as $s) {
            if ('' != trim($s['category']) && '' != trim($s['param_name'])) {
                $param_value = trim($s['param_value']);
                if ('{' == substr($param_value, 0, 1) && '}' == substr($param_value, -1)) {
                    $param_value = json_decode($param_value, true);
                }
                $config_ary[trim($s['category'])][trim($s['param_name'])] = $param_value;
            }
        }
        //$this->helper->debug($config_ary);

        $config_str = "<?php \n# This file automatically generated by Laravel CMS, do not edit it manually \n\n return ".var_export($config_ary, true)."; \n";

        $config_str = $this->parseCmsStr($config_str);

        $config_file = storage_path('app/laravel-cms/settings.php');

        if (! file_exists(dirname($config_file))) {
            mkdir(dirname($config_file), 0755);
        }

        return file_put_contents($config_file, $config_str);

        //return $config_str;
    }

    public function getAdminMenu()
    {
        $menu_links  = $this->s('system.admin_menu_links');
        $all_plugins = $this->s('top.plugin');
        $menu_str    = '';
        //$this->debug($all_plugins);
        if (is_array($menu_links)) {
            foreach ($menu_links as $name=>$link) {
                if (is_array($link)) {
                    if ('dropdown' == $link['style']) {
                        $menu_str .= '<div class="btn-group">
                '.$link['button'].'
                <div class="dropdown-menu">';
                        foreach ($link['items'] as $item) {
                            $menu_str .= $item;
                        }
                        if ('dashboard' == $name) {
                            foreach ($all_plugins as $param_name => $p) {
                                if (isset($p['plugin_type']) && 'standalone' == $p['plugin_type'] && isset($p['tab_name'])) {
                                    $menu_str .= (isset($p['hide_in_menu']) && $p['hide_in_menu']) ? '' : '<a class="dropdown-item" href="'.route('LaravelCmsAdminPlugins.show', $param_name, false).'">'.strip_tags($p['tab_name'], '<i>').'</a>';
                                }
                            }
                        }

                        $menu_str .= '</div>
            </div>';
                    }
                } else {
                    $menu_str .= $link;
                }
            }
        }

        return $menu_str;
    }

    public function loadPluginJs($type)
    {
        $all_plugins = $this->s('top.plugin');
        $js_str      = '';
        if ('js_for_all_admin_pages' == $type) {
            foreach ($all_plugins as $param_name => $p) {
                if (isset($p['plugin_type']) && 'standalone' == $p['plugin_type'] && isset($p[$type])) {
                    $js_str .= '<script src="'.$this->assetUrl('../plugins/'.$param_name.'/'.$p[$type]).'"></script>'."\n";
                }
            }
        }

        return $js_str;
    }
}
