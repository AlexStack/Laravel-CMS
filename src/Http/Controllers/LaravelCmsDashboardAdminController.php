<?php

namespace AlexStack\LaravelCms\Http\Controllers;

use Illuminate\Http\Request;
use AlexStack\LaravelCms\Models\LaravelCmsPage;
use AlexStack\LaravelCms\Models\LaravelCmsSetting;
use AlexStack\LaravelCms\Models\LaravelCmsFile;
use AlexStack\LaravelCms\Helpers\LaravelCmsHelper;
use App\Http\Controllers\Controller;

class LaravelCmsDashboardAdminController extends Controller
{
    private $user = null;
    public $helper;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['web', 'auth']); // TODO: must be admin

        $this->helper = new LaravelCmsHelper;
    }

    public function checkUser()
    {
        // return true;
        if (!$this->user) {
            $this->user = $this->helper->hasPermission();
        }
    }

    public function index()
    {
        $this->checkUser();

        $data['helper'] = $this->helper;

        $data['cms_version'] = $this->helper->s('cms_version');
        if (file_exists(base_path('composer.lock'))) {
            $packages = json_decode(file_get_contents(base_path('composer.lock')), true);
            if (isset($packages['packages'])) {
                foreach ($packages['packages'] as $p) {
                    if (strtolower($p['name']) == 'alexstack/laravel-cms') {
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


        if (empty($this->helper->settings)) {
            return redirect()->route('LaravelCmsSettingPages.index');
        } else {
            //return redirect()->route('LaravelCmsAdminPages.index');
            return view('laravel-cms::' . $this->helper->s('template.backend_dir') .  '.dashboard', $data);
        }
    }


    public function dashboard()
    {
        if (empty($this->helper->settings)) {
            return redirect()->route('LaravelCmsSettingPages.index');
        }
        return redirect()->route('LaravelCmsAdmin.index');
    }
}
