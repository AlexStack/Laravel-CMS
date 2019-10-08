<?php

namespace AlexStack\LaravelCms\Http\Controllers;

use AlexStack\LaravelCms\Helpers\LaravelCmsHelper;
use AlexStack\LaravelCms\Models\LaravelCmsSetting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LaravelCmsPluginAdminController extends Controller
{
    private $user = null;
    private $helper;
    private $plugin;

    public function __construct(LaravelCmsHelper $helper)
    {
        $this->helper = $helper;
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
    }

    public function show($plugin)
    {
        $this->checkUser();
        //$this->helper->debug(request()->all());

        $this->plugin = $this->getPlugin($plugin);

        if ('index' == request()->action) {
            $action = 'index';
        } elseif ('create' == request()->action) {
            $action = 'create';
        } else {
            $action = 'show';
        }

        return $this->customPluginFunc($action, request()->all(), $this->plugin);
    }

    public function create()
    {
        $this->checkUser();
    }

    public function store()
    {
        $this->checkUser();

        if ('' != trim(request()->plugin_name)) {
            $plugin = trim(request()->plugin_name);
        }
        $this->plugin = $this->getPlugin($plugin);

        return $this->customPluginFunc('store', request()->all(), $this->plugin);
    }

    public function edit($plugin)
    {
        $this->checkUser();

        $this->plugin = $this->getPlugin($plugin);

        return $this->customPluginFunc('edit', request()->all(), $this->plugin);
    }

    public function update($plugin)
    {
        $this->checkUser();

        $this->plugin = $this->getPlugin($plugin);

        return $this->customPluginFunc('update', request()->all(), $this->plugin);
    }

    public function destroy($plugin)
    {
        $this->checkUser();

        $this->plugin = $this->getPlugin($plugin);

        return $this->customPluginFunc('destroy', request()->all(), $this->plugin);
    }

    /**
     * other methods.
     */
    public function getPlugin($plugin)
    {
        if (is_numeric($plugin)) {
            $rs = LaravelCmsSetting::where('category', 'plugin')->where('id', $plugin)->first();
        } else {
            $rs = LaravelCmsSetting::where('category', 'plugin')->where('param_name', $plugin)->first();
        }
        if (! $rs) {
            exit('Can not find the plugin '.$plugin);
        }
        $plugin_settings = $this->helper->s('plugin.'.$rs->param_name);
        if (! isset($plugin_settings['plugin_type']) || 'standalone' != $plugin_settings['plugin_type']) {
            exit('This is not a standalone plugin '.$plugin);
        }

        return $rs;
    }

    public function customPluginFunc($action = 'return_options', $form_data = null, $plugin = null)
    {
        $plugin_settings = $this->helper->s('plugin.'.$this->plugin->param_name);
        $plugin_class    = trim($plugin_settings['php_class'] ?? '');
        if ('' != $plugin_class && class_exists($plugin_class) && is_callable($plugin_class.'::'.$action)) {
            //echo $plugin_class . '::' . $action . '  --- ';

            return call_user_func([new $plugin_class(), $action], $form_data, $plugin, $plugin_settings);
        } else {
            return null;
        }
    }
}
