<?php

namespace AlexStack\LaravelCms\Http\Controllers;

use AlexStack\LaravelCms\Helpers\LaravelCmsHelper;
use AlexStack\LaravelCms\Repositories\LaravelCmsDashboardAdminRepository;
use App\Http\Controllers\Controller;

class LaravelCmsDashboardAdminController extends Controller
{
    private $user = null;
    private $helper;
    private $repo;

    public function __construct(LaravelCmsDashboardAdminRepository $repo, LaravelCmsHelper $helper)
    {
        $this->repo   = $repo;
        $this->helper = $helper;

        $this->repo->setHelper($helper);
    }

    public function checkUser()
    {
        // return true;
        if (! $this->user) {
            $this->user = $this->helper->hasPermission();
        }
    }

    public function index()
    {
        $this->checkUser();

        if ('yes' == request()->show_phpinfo) {
            phpinfo();
            exit();
        }

        $data = $this->repo->index();

        if (empty($this->helper->settings)) {
            return redirect()->route('LaravelCmsAdminSettings.index');
        } else {
            return view($this->helper->bladePath('dashboard', 'b'), $data);
        }
    }

    // for admin homepage eg. /cmsadmin without /dashboard
    public function dashboard()
    {
        if (empty($this->helper->settings)) {
            return redirect()->route('LaravelCmsAdminSettings.index');
        }

        return redirect()->route('LaravelCmsAdmin.index');
    }

    public function show($id)
    {
        $data = $this->repo->show($id);

        return $data;
    }
}
