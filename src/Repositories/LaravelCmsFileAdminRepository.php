<?php

namespace AlexStack\LaravelCms\Repositories;

use AlexStack\LaravelCms\Helpers\LaravelCmsHelper;
use AlexStack\LaravelCms\Models\LaravelCmsFile;
use AlexStack\LaravelCms\Models\LaravelCmsSetting;

class LaravelCmsFileAdminRepository extends BaseRepository
{
    /**
     * Configure the Model.
     **/
    public function model()
    {
        return LaravelCmsFile::class;
    }

    /**
     * Controller methods.
     */
    public function index()
    {
        $keyword       = request()->keyword;
        $data['files'] = LaravelCmsFile::when($keyword, function ($query, $keyword) {
            return $query->where('title', 'like', '%'.trim($keyword).'%');
        })
            ->orderBy('updated_at', 'desc')
            ->paginate($this->helper->s('file.number_per_page') ?? 12);

        $data['helper'] = $this->helper;

        return $data;
    }

    public function show($id)
    {
        if (request()->generate_image && request()->width) {
            $file = LaravelCmsFile::find($id);
            $url  = $this->helper->imageUrl($file, request()->width, request()->height);
            if (request()->return_url) {
                return $url;
            }

            return redirect()->to($url);
        }

        return 'generate_image error';
    }

    public function create()
    {
        $data['helper'] = $this->helper;

        return $data;
    }

    public function store($form_data)
    {
        $all_file_data = [];
        $rs            = $this->handleUpload($form_data, $all_file_data);

        return $rs;
    }

    public function update($form_data, $id)
    {
        return true;
    }

    public function edit($id)
    {
        $data['file']   = LaravelCmsFile::find($id);
        $data['helper'] = $this->helper;

        return $data;
    }

    public function destroy($id)
    {
        $file = LaravelCmsFile::find($id);

        $original_file_path = public_path($this->helper->imageUrl($file));
        if (file_exists($original_file_path)) {
            unlink($original_file_path);
        }
        if ($file->is_image) {
            $small_img_path = public_path($this->helper->imageUrl($file, $this->helper->s('file.small_image_width')));

            $all_images = glob(dirname($small_img_path).'/'.$id.'_*');

            //$this->helper->debug($all_images);
            array_map('unlink', $all_images);
        }

        $rs = $file->delete();

        return $rs;
    }

    /**
     * Other methods.
     */
    private function handleUpload(&$form_data, &$all_file_data = [])
    {
        $request = request();
        $files   = $request->file('files');

        if ($request->hasFile('files')) {
            foreach ($files as $file) {
                $all_file_data[] = $this->helper->uploadFile($file)->toArray();
            }

            return true;
        }

        return false;
    }

    public function downloadFile($url, $type='plugin')
    {
        if (! ini_get('allow_url_fopen')) {
            abort(response()->json('allow_url_fopen not enable', 503));
        }
        if ('plugin' == $type) {
            if (stripos($url, 'github.com')) {
                if (! class_exists('PharData')) {
                    $url = str_replace('.tar.gz', '.zip', $url);
                }
                $url_ary  = explode('/', $url);
                $temp_dir = $url_ary[3].'---'.$url_ary[4];
            } else {
                $temp_dir = md5($url);
            }
            $file_str = file_get_contents($url);

            $relative_dir = $this->helper->s('file.upload_dir').'/temp/'.$temp_dir;
            $file_abs_dir = public_path($relative_dir);
            if (! file_exists($file_abs_dir)) {
                mkdir($file_abs_dir, 0755, true);
            }
            $file_name = basename($url);
            $file_path = $file_abs_dir.'/'.$file_name;
            file_put_contents($file_path, $file_str);

            return $relative_dir.'/'.$file_name;
        }
    }

    public function extractFile($file, $type='plugin')
    {
        $file_abs_path = public_path($file);

        if (! class_exists('PharData') && ! class_exists('ZipArchive')) {
            abort(response()->json('PharData or ZipArchive not enable', 503));
        }

        if ('plugin' == $type) {
            if (class_exists('PharData')) {
                $phar = new \PharData($file_abs_path);
                $phar->extractTo(dirname($file_abs_path), null, true);
            } elseif (class_exists('ZipArchive')) {
                $zip = new \ZipArchive();
                $res = $zip->open($file_abs_path);
                if (true === $res) {
                    $zip->extractTo(dirname($file_abs_path));
                    $zip->close();
                } else {
                    abort(response()->json('ZipArchive ErrorCode:'.$res, 503));
                }
            }
        }

        return $file;
    }

    public function installPackage($package_file, $type='plugin')
    {
        if ('plugin' == $type) {
            return $this->installPlugin($package_file);
        }

        return 'installPackage';
    }

    public function installPlugin($package_file)
    {
        $package_abs_dir = public_path(dirname($package_file));
        $package_version = str_replace(['.tar.gz', '.zip'], '', basename($package_file));

        $package_dirs = glob($package_abs_dir.'/*', GLOB_ONLYDIR);

        if (! isset($package_dirs[0])) {
            $result['success']         = false;
            $result['error_message']   = 'Can not find the package extraction folder!';

            return response()->json($result);
        }
        usort($package_dirs, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        // check plugin file structure
        $extract_dir  = $package_dirs[0]; // the latest edit folder
        $core_files   = ['composer.json', 'src/resources/views/plugins', 'src/database'];
        foreach ($core_files as $file) {
            if (! file_exists($extract_dir.'/'.$file)) {
                $result['success']         = false;
                $result['error_message']   = 'Wrong plugin file structure, '.$file.' not exists!';

                return response()->json($result);
            }
        }
        // check composer.json autoload->psr-4
        $composer_json = json_decode(file_get_contents($extract_dir.'/composer.json'), true);
        if (! $composer_json) {
            $result['success']         = false;
            $result['error_message']   = 'composer.json can not decode!';

            return response()->json($result);
        }
        if (! isset($composer_json['autoload']['psr-4'])) {
            $result['success']         = false;
            $result['error_message']   = 'autoload->psr-4 not found in the composer.json!';

            return response()->json($result);
        }

        if (! isset($composer_json['extra']['laravel-cms']['plugin-param-name'])) {
            $result['success']         = false;
            $result['error_message']   = 'extra->laravel-cms->plugin-param-name not found in the composer.json!';

            return response()->json($result);
        }

        foreach ($composer_json['autoload']['psr-4'] as $namespace => $package_dir) {
            if ('src/' == $package_dir) {
                $original_namespace = $namespace;
            }
        }
        if (! isset($original_namespace) || strpos($original_namespace, '\\\\LaravelCms\\\\Plugins\\\\')) {
            $result['success']                    = false;
            $result['error_message']              = 'psr-4: YourName\\LaravelCms\\Plugins\\PackageName => src/ not found in the composer.json!';
            $result['original_namespace']         = $original_namespace ?? '';

            return response()->json($result);
        }

        // generate namespace
        $temp_ary           = explode('\\LaravelCms\\', $original_namespace);
        $new_namespace      = 'App\\LaravelCms\\'.$temp_ary[1];
        $plugin_folder_name = explode('\\', $new_namespace)[3];
        $src_dirs           = glob($extract_dir.'/src/*', GLOB_ONLYDIR);
        $replace_results    = '';

        // replace namespace
        foreach ($src_dirs as $dir) {
            if (in_array(basename($dir), ['resources', 'database', 'assets'])) {
                continue;
            }
            $php_files       = glob($dir.'/*.php');
            foreach ($php_files as $file) {
                $replace_results .= ' | '.$file.':';
                $file_str = file_get_contents($file);
                if (strpos($file_str, $original_namespace)) {
                    file_put_contents($file, str_replace($original_namespace, $new_namespace, $file_str));
                    $replace_results .= 'REPLACED';
                } else {
                    $replace_results .= 'SKIPPED';
                }
            }
        }

        // replace migrations sql
        if (file_exists($extract_dir.'/src/database/migrations')) {
            $php_files = glob($extract_dir.'/src/database/migrations/*.php');
            foreach ($php_files as $file) {
                $replace_results .= ' | '.$file.':';
                $file_str               = file_get_contents($file);
                $sql_original_namespace = str_replace('\\', '\\\\\\\\', $original_namespace);
                $sql_new_namespace      = str_replace('\\', '\\\\\\\\', $new_namespace);

                if (strpos($file_str, $sql_original_namespace)) {
                    file_put_contents($file, str_replace($sql_original_namespace, $sql_new_namespace, $file_str));
                    $replace_results .= 'REPLACED';
                } else {
                    $replace_results .= 'SKIPPED';
                }
            }
            // php artisan migrate, abs path seems not work on windows
            $exitCode = \Artisan::call('migrate', ['--path'=> './public/'.dirname($package_file).'/'.basename($extract_dir).'/src/database/migrations/', '--force'=>true]);

            $replace_results .= ' | Artisan migrate:'.$exitCode;
        }

        // move view files
        $target_dir   = base_path('resources/views/vendor/laravel-cms/plugins');
        $backup_dir   = storage_path('app/laravel-cms/backups/views/plugins');
        $source_files = glob($extract_dir.'/src/resources/views/plugins/*', GLOB_ONLYDIR);

        $this->moveCmsFiles($source_files, $target_dir, $backup_dir);

        // if (! file_exists($view_backup_dir)) {
        //     mkdir($view_backup_dir, 0755, true);
        // }
        // $plugin_dirs = glob($extract_dir.'/src/resources/views/plugins/*', GLOB_ONLYDIR);
        // foreach ($plugin_dirs as $dir) {
        //     $folder_name = basename($dir);
        //     if (file_exists($plugin_view_path.'/'.$folder_name)) {
        //         $new_name = $folder_name.'-bak-'.date('YmdHis');
        //         rename($plugin_view_path.'/'.$folder_name, $view_backup_dir.'/'.$new_name);
        //     }
        //     rename($dir, $plugin_view_path.'/'.$folder_name);
        // }

        // move lang files, lang/en lang/zh etc.
        $target_dir   = base_path('resources/lang/vendor/laravel-cms');
        $backup_dir   = storage_path('app/laravel-cms/backups/lang');
        $source_dirs  = glob($extract_dir.'/src/resources/lang/*', GLOB_ONLYDIR);

        foreach ($source_dirs as $dir) {
            $lang_dir     = basename($dir);
            $source_files = glob($dir.'/plugin-*.php');

            $this->moveCmsFiles(
                $source_files,
                $target_dir.'/'.$lang_dir,
                $backup_dir.'/'.$lang_dir
            );
        }

        // move assets files
        $target_dir   = public_path('laravel-cms/plugins');
        $backup_dir   = storage_path('app/laravel-cms/backups/assets/plugins');
        $source_files = glob($extract_dir.'/src/assets/plugins/*', GLOB_ONLYDIR);

        $this->moveCmsFiles($source_files, $target_dir, $backup_dir);
        // if (! file_exists($asset_backup_dir)) {
        //     mkdir($asset_backup_dir, 0755, true);
        // }
        // if (! file_exists($plugin_asset_path)) {
        //     mkdir($plugin_asset_path, 0755, true);
        // }

        // $plugin_dirs = glob($extract_dir.'/src/assets/plugins/*', GLOB_ONLYDIR);
        // foreach ($plugin_dirs as $dir) {
        //     $folder_name = basename($dir);
        //     if (file_exists($plugin_asset_path.'/'.$folder_name)) {
        //         $new_name = $folder_name.'-bak-'.date('YmdHis');
        //         rename($plugin_asset_path.'/'.$folder_name, $asset_backup_dir.'/'.$new_name);
        //     }
        //     rename($dir, $plugin_asset_path.'/'.$folder_name);
        // }

        // move php class files to app/LaravelCms
        $target_dir   = base_path('app/LaravelCms/Plugins/'.$plugin_folder_name);
        $backup_dir   = storage_path('app/laravel-cms/backups/php-files/plugins');
        $source_files = glob($extract_dir.'/src/*', GLOB_ONLYDIR);
        $ignore_files = ['resources', 'database', 'assets'];

        $this->moveCmsFiles($source_files, $target_dir, $backup_dir, $ignore_files);

        // if (! file_exists($plugin_class_path)) {
        //     mkdir($plugin_class_path, 0755, true);
        // } else {
        //     if (! file_exists($class_backup_dir)) {
        //         mkdir($class_backup_dir, 0755, true);
        //     }

        //     $new_name = $plugin_folder_name.'-bak-'.date('YmdHis');
        //     rename($plugin_class_path, $class_backup_dir.'/'.$new_name);

        //     mkdir($plugin_class_path, 0755, true);
        // }
        // $plugin_dirs = glob($extract_dir.'/src/*', GLOB_ONLYDIR);
        // foreach ($plugin_dirs as $dir) {
        //     $folder_name = basename($dir);
        //     if (in_array($folder_name, ['resources', 'database', 'assets'])) {
        //         continue;
        //     }
        //     // if (file_exists($plugin_class_path.'/'.$folder_name)) {
        //     //     $new_name = $folder_name.'-bak-'.date('YmdHis');
        //     //     rename($plugin_class_path.'/'.$folder_name, $plugin_class_path.'/'.$new_name);
        //     // }
        //     rename($dir, $plugin_class_path.'/'.$folder_name);
        // }

        // generate new settings file
        $this->helper->rewriteConfigFile();
        $this->helper = new LaravelCmsHelper(); // load new cms settings

        // update plugin setting version
        $plugin_param_name  = $composer_json['extra']['laravel-cms']['plugin-param-name'];
        $plugin_param_value = $this->helper->s('plugin.'.$plugin_param_name);
        if ($plugin_param_value) {
            $plugin_param_value['version']          = $package_version;
            $plugin_param_value['github_full_name'] = $composer_json['name'];

            $plugin_setting = LaravelCmsSetting::where('category', 'plugin')->where('param_name', $plugin_param_name)->first();

            $plugin_setting->update([
                'param_value' => json_encode($plugin_param_value),
            ]);
        } else {
            $plugin_setting = LaravelCmsSetting::where('category', 'plugin')->where('param_name', $plugin_param_name)->first();
        }

        if (! isset($plugin_setting->param_name)) {
            $result['success']         = false;
            $result['error_message']   = 'Can not find plugin.'.$plugin_param_name.' in the setting table. Make sure the migrate record not exists & re-install it!';
            // php artisan migrate, abs path seems not work on windows
            $result['migrate_reset']         = \Artisan::call('migrate:reset', ['--path'=> './public/'.dirname($package_file).'/'.basename($extract_dir).'/src/database/migrations/', '--force'=>true]);

            return response()->json($result);
        }

        // delete files
        \File::deleteDirectory($package_abs_dir);

        // generate new settings file
        $this->helper->rewriteConfigFile();

        $result['success']       = true;
        $result['error_message'] = '';
        $result['param_name']    = $plugin_param_name;
        $result['plugin_id']     = $plugin_setting->id ?? 0;
        $result['plugin_type']   = $plugin_param_value['plugin_type'] ?? 'page-tab';

        // $result['original_namespace']         = $original_namespace ?? '';
        // $result['new_namespace']              = $new_namespace ?? '';
        // $result['plugin_folder_name']         = $plugin_folder_name ?? '';
        // $result['replace_results']            = $replace_results ?? '';
        // $result['sql_original_namespace']     = $sql_original_namespace ?? '';
        // $result['package_abs_dir']            = $package_abs_dir ?? '';

        return response()->json($result);
    }

    public function upgradeCmsViaBrowser($cms_version, $old_version)
    {
        $need_composer  = true;
        $cms_source_dir = storage_path('app/laravel-cms/backups/cms-source-code/git-clone-cms-'.$cms_version.'-'.date('Y-m-d-His'));
        $cms_backup_dir = storage_path('app/laravel-cms/backups/cms-source-code/vendor-laravel-cms-'.$old_version.'-'.date('Y-m-d-His'));
        $cms_vendor_dir = base_path('vendor/alexstack/laravel-cms');

        if (false !== strpos(ini_get('disable_functions'), 'exec')) {
            $result['success']       = false;
            $result['error_message'] = 'Your server do not support upgrade the CMS online via browser, please upgrade the CMS via composer command';

            return response()->json($result);
        }

        exec('git clone --branch '.$cms_version.' --depth 1 https://github.com/AlexStack/Laravel-CMS.git '.$cms_source_dir);

        if (file_exists($cms_source_dir.'/composer.json')) {
            $old_composer = json_decode(file_get_contents($cms_vendor_dir.'/composer.json'), true);
            $new_composer = json_decode(file_get_contents($cms_source_dir.'/composer.json'), true);
            if (isset($new_composer['require']) && $new_composer['require'] == $old_composer['require']) {
                $need_composer = false;
            }
        }

        if ($need_composer) {
            $result['success']       = false;
            $result['error_message'] = 'This new version can NOT upgrade via browser, needs to upgrade via composer command!';

            return response()->json($result);
        }

        $rs = @rename($cms_vendor_dir, $cms_backup_dir);
        if ($rs) {
            $rs = @rename($cms_source_dir, $cms_vendor_dir);
            if ($rs) {
                exec('git --git-dir='.$cms_vendor_dir.'/.git --work-tree='.$cms_vendor_dir.' remote add composer https://github.com/AlexStack/Laravel-CMS.git');

                $rs = LaravelCmsSetting::updateOrCreate(
                    ['param_name' => 'cms_version', 'category' => 'system'],
                    ['param_name' => 'cms_version', 'category' => 'system', 'param_value'=>$cms_version, 'enabled'=>1]
                );

                exec('php '.base_path('artisan').' laravelcms --action=upgrade --silent=yes');

                $result['success']         = true;
                $result['error_message']   = 'Upgrade CMS successful!';

                return response()->json($result);
            }
        }

        @rename($cms_source_dir, $cms_source_dir.'-pending-del');

        $result['success']         = false;
        $result['error_message']   = 'Upgrade CMS failed!';

        return response()->json($result);
    }

    public function moveCmsFiles($source_files, $target_dir, $backup_dir, $ignore_files=[])
    {
        if (! file_exists($backup_dir)) {
            mkdir($backup_dir, 0755, true);
        }
        if (! file_exists($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        foreach ($source_files as $dir) {
            $folder_name = basename($dir);
            if (in_array($folder_name, $ignore_files)) {
                continue;
            }
            if (file_exists($target_dir.'/'.$folder_name)) {
                $new_name = $folder_name.'-bak-'.date('YmdHis');
                rename($target_dir.'/'.$folder_name, $backup_dir.'/'.$new_name);
            }
            rename($dir, $target_dir.'/'.$folder_name);
        }

        return true;
    }
}
