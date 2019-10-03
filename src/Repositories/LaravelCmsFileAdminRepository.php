<?php

namespace AlexStack\LaravelCms\Repositories;

use AlexStack\LaravelCms\Models\LaravelCmsFile;

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

        $package_dirs = glob($package_abs_dir.'/*', GLOB_ONLYDIR);

        if (! isset($package_dirs[0])) {
            $result['success']         = false;
            $result['error_message']   = 'Can not find the package extraction folder!';

            return response()->json($result);
        }
        // check plugin file structure
        $extract_dir  = $package_dirs[0];
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
            $exitCode = \Artisan::call('migrate', ['--path'=> './public/'.dirname($package_file).'/'.basename($extract_dir).'/src/database/migrations/', '--force']);

            $replace_results .= ' | Artisan migrate:'.$exitCode;
        }

        // move view files
        $plugin_view_path = base_path('resources/views/vendor/laravel-cms/plugins');
        if (! file_exists($plugin_view_path)) {
            mkdir($plugin_view_path, 0755, true);
        }
        $plugin_dirs = glob($extract_dir.'/src/resources/views/plugins/*', GLOB_ONLYDIR);
        foreach ($plugin_dirs as $dir) {
            $folder_name = basename($dir);
            if (file_exists($plugin_view_path.'/'.$folder_name)) {
                $new_name = $folder_name.'-bak-'.date('YmdHis');
                rename($plugin_view_path.'/'.$folder_name, $plugin_view_path.'/'.$new_name);
            }
            rename($dir, $plugin_view_path.'/'.$folder_name);
        }

        // move php class files
        $plugin_class_path = base_path('app/LaravelCms/Plugins/'.$plugin_folder_name);
        if (! file_exists($plugin_class_path)) {
            mkdir($plugin_class_path, 0755, true);
        }
        $plugin_dirs = glob($extract_dir.'/src/*', GLOB_ONLYDIR);
        foreach ($plugin_dirs as $dir) {
            $folder_name = basename($dir);
            if (in_array($folder_name, ['resources', 'database', 'assets'])) {
                continue;
            }
            if (file_exists($plugin_class_path.'/'.$folder_name)) {
                $new_name = $folder_name.'-bak-'.date('YmdHis');
                rename($plugin_class_path.'/'.$folder_name, $plugin_class_path.'/'.$new_name);
            }
            rename($dir, $plugin_class_path.'/'.$folder_name);
        }

        // delete files
        \File::deleteDirectory($package_abs_dir);

        // rewriteConfigFile
        $this->helper->rewriteConfigFile();

        $result['success']       = true;
        $result['error_message'] = '';

        // $result['original_namespace']         = $original_namespace ?? '';
        // $result['new_namespace']              = $new_namespace ?? '';
        // $result['plugin_folder_name']         = $plugin_folder_name ?? '';
        // $result['replace_results']            = $replace_results ?? '';
        // $result['sql_original_namespace']     = $sql_original_namespace ?? '';
        // $result['package_abs_dir']            = $package_abs_dir ?? '';

        return response()->json($result);
    }
}
