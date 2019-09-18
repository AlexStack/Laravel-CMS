<?php

namespace AlexStack\LaravelCms\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use AlexStack\LaravelCms\Helpers\LaravelCmsHelper;
use AlexStack\LaravelCms\Repositories\LaravelCmsSettingRepository;

class LaravelCmsSettingAdminController extends Controller
{
    private $user = null;
    private $helper;
    private $repo;

    public function __construct(LaravelCmsSettingRepository $repo, LaravelCmsHelper $helper)
    {
        $this->repo     = $repo;
        $this->helper   = $helper;

        $this->repo->setHelper($helper);
    }

    public function checkUser()
    {
        if (!$this->user) {
            $this->user = $this->helper->hasPermission();
        }
    }

    public function index()
    {
        $this->checkUser();

        $data = $this->repo->index();

        return view('laravel-cms::' . $this->helper->s('template.backend_dir') .  '.setting-list', $data);
    }



    public function edit($id)
    {
        $this->checkUser();


        $data = $this->repo->edit($id);

        return view('laravel-cms::' . $this->helper->s('template.backend_dir') .  '.setting-edit', $data);
    }

    public function create()
    {
        $this->checkUser();

        $data = $this->repo->create();

        return view('laravel-cms::' . $this->helper->s('template.backend_dir') .  '.setting-create', $data);
    }


    public function store(Request $request)
    {
        $this->checkUser();

        $form_data = $request->all();
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
        $rs = $this->repo->update($form_data, $setting);

        if ($form_data['return_to_the_list']) {
            return redirect()->route('LaravelCmsAdminSettings.index', ['category' => $form_data['category']]);
        }
        return back()->withInput();
    }



    public function destroy(Request $request, $id)
    {
        $this->checkUser();
        $rs = $this->repo->destroy($id);

        return redirect()->route('LaravelCmsAdminSettings.index');
    }
}
