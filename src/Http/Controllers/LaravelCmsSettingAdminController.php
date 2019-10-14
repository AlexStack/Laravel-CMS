<?php

namespace AlexStack\LaravelCms\Http\Controllers;

use AlexStack\LaravelCms\Helpers\LaravelCmsHelper;
use AlexStack\LaravelCms\Repositories\LaravelCmsSettingAdminRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LaravelCmsSettingAdminController extends Controller
{
    private $user = null;
    private $helper;
    private $repo;

    public function __construct(LaravelCmsSettingAdminRepository $repo, LaravelCmsHelper $helper)
    {
        $this->repo   = $repo;
        $this->helper = $helper;

        $this->repo->setHelper($helper);
    }

    public function checkUser()
    {
        if (! $this->user) {
            $this->user = $this->helper->hasPermission();
        }
    }

    public function index()
    {
        $this->checkUser();

        $data = $this->repo->index();

        return view($this->helper->bladePath('setting-list', 'b'), $data);
    }

    public function edit($id)
    {
        $this->checkUser();

        $data = $this->repo->edit($id);

        return view($this->helper->bladePath('setting-edit', 'b'), $data);
    }

    public function create()
    {
        $this->checkUser();

        $data = $this->repo->create();

        return view($this->helper->bladePath('setting-create', 'b'), $data);
    }

    public function store(Request $request)
    {
        $this->checkUser();

        $form_data            = $request->all();
        $form_data['user_id'] = $this->user->id ?? null;

        $rs = $this->repo->store($form_data);

        if ($form_data['return_to_the_list']) {
            return redirect()->route('LaravelCmsAdminSettings.index', ['category' => $rs->category]);
        }

        return redirect()->route('LaravelCmsAdminSettings.edit', ['setting' => $rs->id]);
    }

    public function update(Request $request, $setting)
    {
        $this->checkUser();

        $form_data = $request->all();
        $rs        = $this->repo->update($form_data, $setting);

        if ($form_data['return_to_the_list']) {
            return redirect()->route('LaravelCmsAdminSettings.index', ['category' => $form_data['category']]);
        }

        return back()->withInput();
    }

    public function destroy(Request $request, $id)
    {
        $this->checkUser();
        $rs = $this->repo->destroy($id);

        return redirect()->route('LaravelCmsAdminSettings.index', ['category' => $request->category]);
    }
}
