<?php

namespace AlexStack\LaravelCms\Helpers;

use AlexStack\LaravelCms\Models\LaravelCmsPage;


class LaravelCmsHelper
{

    static public function imageUrl($img_obj, $width = null, $height = null, $resize_type = 'ratio')
    {
        if (!isset($img_obj->id)) {
            return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAACWBAMAAADOL2zRAAAAG1BMVEX59/cUAQFNPj6jmprc2NhpXV0wHx+/ubmGfHzpsC2oAAAACXBIWXMAAA7EAAAOxAGVKw4bAAACC0lEQVRoge2VPW/bMBCGiUiONOb8QWWMEKDpWAFF2lFakow2aqQdqzppO9pBA3S0gCC/u+/RqqVELFynmor3GfxB3j3kkRRlDCGEEEIIIYQQQgghZA/SkX6GMn3SGsn6JS6b9+dylr5co/5cX7XInlwL1fTk+pwmtSu8ltu8cYXy49GemavyyxIt96V97Xpe2XmhGY/b4JarsLWrEpHjtuscDe9KEaQO8FNu0DPDd4WGrAluuQJ41BXLmbmra9u45G0gdmEyDFZN8st0YjQorCAPWsEtl0GR6iqG+nfcciU61xz5uZv3oRhTwBegwwXPxh0XitRY16OxW9eJMSuXuwygRJ1waxAG1zJNcdxxxbJWV4lUc2BbrukmPJKla8T8NkFZommY6LDjwjia546Fjr11YTmK0W/X5fsULhe0SkypeyG26yrsTleQai5c6x2uWB52uVIZfvrZnteJ6eIKLxPvejWuWOae9fK6Vta7j43rUIc4gGv2ZB8nHlcs2/OV/NmVwaXpkZ4vvRFmiceFpfSd+8Y1kHl4L1i3gXzEuR8jeJHfPbsQatfK+zw2rlD37VTeuOfRaqEa/KzE2jVQV3Qt33O/C7eF/WBKBAW4J7Lx5lJZetZ/X6qjHiS1xnsg9idzNU93B/4FF/IQnrvX4L8T6WP4rReVMVepPe1JRQghhBBCCCGEEPL/8wuP/XKfB47dPQAAAABJRU5ErkJggg==';
        }
        if (!is_numeric($width)) {
            $width = null;
        }
        if (!is_numeric($height)) {
            $height = null;
        }


        if ($img_obj->suffix == 'svg' || ($width == null && $height == null)) {
            $original_img_url = '/storage/' . config('laravel-cms.upload_dir') . '/' . $img_obj->path;
            return $original_img_url;
        }

        if (config('laravel-cms.image_encode') == 'jpg') {
            $suffix = 'jpg';
        } else {
            $suffix = $img_obj->suffix;
        }

        $filename   = $img_obj->id . '_' . ($width ?? 'auto') . '_' . ($height ?? 'auto') . '_' . $resize_type . '.' . $suffix;

        $related_dir = 'storage/' . config('laravel-cms.upload_dir') . '/optimized/' . substr($img_obj->id, -2);

        $abs_real_dir = public_path($related_dir);
        $abs_real_path = $abs_real_dir . '/' . $filename;
        $web_url = '/' . $related_dir . '/' . $filename;

        if (file_exists($abs_real_path) && filemtime($abs_real_path) > time() - config('laravel-cms.image_reoptimize_time')) {
            return $web_url;
            //return $abs_real_path . ' - already exists - ' . $web_url;
        }

        if (!file_exists($abs_real_dir)) {
            mkdir($abs_real_dir, 0755, true);
        }

        $original_img = public_path('storage/' . config('laravel-cms.upload_dir') . '/' . $img_obj->path);

        //self::debug($original_img);

        // resize the image to a width of 800 and constrain aspect ratio (auto height)
        $new_img = \Intervention\Image\ImageManagerStatic::make($original_img)->orientate()->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        if ($suffix == 'jpg' || $suffix == 'jpeg') {
            $new_img->encode('jpg');
        }
        $new_img->save($abs_real_path, 75);

        return $web_url;
        // return $abs_real_path . ' optimized image created ' . $width;
    }


    static public function debug($data, $exit = 'exit')
    {
        if (is_a($data, 'Illuminate\Database\Eloquent\Collection')) {
            $data = $data->toArray();
        }
        echo '<pre>' . var_export($data, true) . '</pre>';
        echo '<hr>Debug Time: ' . date('Y-m-d H:i:s') . '<hr>';
        if ($exit != 'no_exit') {
            exit();
        }
    }

    public function menus()
    {
        $data['menus'] = LaravelCmsPage::with('menus:title,menu_title,id,parent_id,slug,redirect_url,menu_enabled')
            ->whereNull('parent_id')
            ->where('menu_enabled', 1)
            ->orderBy('sort_value', 'desc')
            ->orderBy('id', 'desc')
            ->get(['title', 'menu_title', 'id', 'parent_id', 'slug', 'redirect_url', 'menu_enabled']);

        //var_dump($data['menus']->toArray());
        //$this->debug($data['menus']);

        return $data['menus'];
    }


    public function url($page)
    {
        if (!$page->slug) {
            $page->slug = $page->id . '.html';
        }
        if (trim($page->redirect_url) != '') {
            return trim($page->redirect_url);
        }
        if ($page->slug == 'homepage') {
            return route('LaravelCmsPages.index', [], false);
        }
        return route('LaravelCmsPages.show', $page->slug, false);
    }


    public function assetUrl($file)
    {
        $url = 'laravel-cms/' . config('laravel-cms.template_frontend_dir') . '/' . $file;

        $abs_real_path = public_path($url);

        if (file_exists($abs_real_path)) {
            $url .= '?last_modify_time=' . date('Ymd-His', filemtime($abs_real_path));
        } else {
            $url .= '?file_not_exists_please_publish_it_first';
        }
        return '/' . $url;
    }
}
