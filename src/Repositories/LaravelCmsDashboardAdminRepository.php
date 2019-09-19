<?php

namespace AlexStack\LaravelCms\Repositories;

use AlexStack\LaravelCms\Models\LaravelCmsFile;
use AlexStack\LaravelCms\Models\LaravelCmsPage;
use AlexStack\LaravelCms\Models\LaravelCmsSetting;

class LaravelCmsDashboardAdminRepository extends BaseRepository
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
        $data['helper']      = $this->helper;
        $data['cms_version'] = $this->helper->s('cms_version');
        if (file_exists(base_path('composer.lock'))) {
            $packages = json_decode(file_get_contents(base_path('composer.lock')), true);
            if (isset($packages['packages'])) {
                foreach ($packages['packages'] as $p) {
                    if ('alexstack/laravel-cms' == strtolower($p['name'])) {
                        $data['cms_version'] = $p['version'];
                    }
                }
            }
            //$this->helper->debug($data['cms_version']);
            //$data['cms_version'] =
        }

        $data['latest_pages'] = LaravelCmsPage::orderBy('updated_at', 'desc')
            ->limit(10)
            ->get(['id', 'title', 'menu_title', 'created_at', 'updated_at']);

        $data['latest_settings'] = LaravelCmsSetting::orderBy('updated_at', 'desc')
            ->limit(10)
            ->get(['id', 'category', 'param_name', 'created_at', 'updated_at']);

        $data['latest_files'] = LaravelCmsFile::orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return $data;
    }

    public function show($id)
    {
        if ('logout' == $id) {
            return $this->logout();
        }

        return true;
    }

    public function create()
    {
        return true;
    }

    public function store($form_data)
    {
        return true;
    }

    public function update($form_data, $id)
    {
        return true;
    }

    public function edit($id)
    {
        return true;
    }

    public function destroy($id)
    {
        return true;
    }

    /**
     * Other methods.
     */
    public function logout()
    {
        //logout user
        auth()->logout();
        // redirect to homepage
        return redirect('/');
    }
}
