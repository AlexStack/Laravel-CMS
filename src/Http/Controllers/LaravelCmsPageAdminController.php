<?php

namespace AlexStack\LaravelCms\Http\Controllers;

use AlexStack\LaravelCms\Helpers\LaravelCmsHelper;
use AlexStack\LaravelCms\Repositories\LaravelCmsPageAdminRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LaravelCmsPageAdminController extends Controller
{
    private $user = null;
    private $helper;
    private $repo;

    public function __construct(LaravelCmsPageAdminRepository $repo, LaravelCmsHelper $helper)
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
        if ('json' == request()->response_type) {
            $data = $this->repo->index();

            unset($data['helper']);
            unset($data['plugins']);

            foreach ($data['all_pages'] as $key => $page) {
                $data['all_pages'][$key]->url = $this->helper->url($page);
                //remove some unused ReactJS api data for speed, it can save around 24% bandwidth transfer
                unset($data['all_pages'][$key]->redirect_url);
                unset($data['all_pages'][$key]->slug);
                unset($data['all_pages'][$key]->status);
                unset($data['all_pages'][$key]->parent_id);
                unset($data['all_pages'][$key]->sort_value);
                // $data['all_pages'][$key]->menu_enabled = (int) $data['all_pages'][$key]->menu_enabled;
            }

            // JSON_UNESCAPED_UNICODE can save around another 20% bandwidth transfer if it is Chinese site
            $rs = response()->json($data['all_pages'])->setEncodingOptions(JSON_UNESCAPED_UNICODE);

            return $rs;
        } elseif (0 === $this->helper->s('system.all_pages.react_js')) {
            $data = $this->repo->index();

            return view($this->helper->bladePath('page-list', 'b'), $data);
        } else {
            $data['helper'] = $this->helper;

            return view($this->helper->bladePath('page-list-react-js', 'b'), $data);
        }
    }

    public function create()
    {
        $this->checkUser();

        $data = $this->repo->create();

        return view($this->helper->bladePath('page-create', 'b'), $data);
    }

    public function store(Request $request)
    {
        $this->checkUser();

        $form_data            = $request->all();
        $form_data['user_id'] = $this->user->id ?? null;

        $rs = $this->repo->store($form_data);

        if ($form_data['return_to_the_list']) {
            return redirect()->route('LaravelCmsAdminPages.index');
        }

        return redirect()->route('LaravelCmsAdminPages.edit', ['page' => $rs->id]);
    }

    public function edit($id)
    {
        $this->checkUser();

        $data = $this->repo->edit($id);

        return view($this->helper->bladePath('page-edit', 'b'), $data);
    }

    public function update(Request $request, $page)
    {
        $this->checkUser();

        $form_data            = $request->all();
        $form_data['user_id'] = $form_data['user_id'] ?? $this->user->id;

        $rs = $this->repo->update($form_data, $page);

        if ($form_data['return_to_the_list']) {
            return redirect()->route('LaravelCmsAdminPages.index');
        }

        return redirect()->route('LaravelCmsAdminPages.edit', ['page' => $page]);
    }

    public function destroy(Request $request, $id)
    {
        $this->checkUser();
        $rs = $this->repo->destroy($id);

        if ('json' == request()->response_type) {
            $result['success']         = $rs;
            $result['success_content'] = 'Page id '.$id.' deleted';
            $result['error_message']   = 'Delete page id '.$id.' failed!';

            return response()->json($result);
        }

        return redirect()->route('LaravelCmsAdminPages.index');
    }
}
