<?php

namespace AlexStack\LaravelCms\Http\Controllers;

use App\Http\Controllers\Controller;
use AlexStack\LaravelCms\Helpers\LaravelCmsHelper;
use AlexStack\LaravelCms\Repositories\LaravelCmsDashboardAdminRepository;

class LaravelCmsDashboardAdminController extends Controller
{
    private $user = null;
    private $helper;
    private $repo;

    public function __construct(LaravelCmsDashboardAdminRepository $repo, LaravelCmsHelper $helper)
    {
        $this->repo = $repo;
        $this->helper = $helper;

        $this->repo->setHelper($helper);
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

        $data = $this->repo->index();

        if (empty($this->helper->settings)) {
            return redirect()->route('LaravelCmsAdminSettings.index');
        } else {
            return view('laravel-cms::'.$this->helper->s('template.backend_dir').'.dashboard', $data);
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
