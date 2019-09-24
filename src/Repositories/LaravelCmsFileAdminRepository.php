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
}
