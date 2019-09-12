<?php

namespace AlexStack\LaravelCms\Console\Commands;

use Illuminate\Console\Command;

class LaravelCMS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravelcms
            {--action=initialize : initialize or install or uninstall}
            {--table_prefix= : Default table_prefix is cms_}
            {--locale= : Default locale is en}
            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Artisan commands for Laravel CMS';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */

    public function handle()
    {
        $options = $this->options();
        //var_dump($options);
        if ($options['action'] == 'initialize' || $options['action'] == 'install') {
            $this->initializeCms($options);
        } else if ($options['action'] == 'uninstall') {
            $this->uninstall($options);
        } else {
            $this->error('Wrong action');
        }
    }
    public function uninstall($options)
    {
        $this->line('<fg=red>****</>');
        $this->line('<fg=red>**** UNINSTALL Amila Laravel CMS ****</>');
        $this->line('<fg=red>****</>');


        if (!$this->confirm('<fg=cyan>**** Remove the CMS database tables? ****</>', true)) {
            $this->error("User aborted! please run the command again.");
            exit();
        }

        $this->call('migrate:reset', [
            '--path' => './vendor/alexstack/laravel-cms/src/database/migrations/'
        ]);

        if (!$this->confirm('<fg=cyan>**** Remove the CMS folders and files? ****</>', true)) {
            $this->error("User aborted! please run the command again.");
            exit();
        }
        $paths = [
            base_path('resources/views/vendor/laravel-cms'),
            base_path('resources/lang/vendor/laravel-cms'),
            public_path('laravel-cms'),
            base_path('config/laravel-cms.php'),
            storage_path('app/laravel-cms'),
            storage_path('app/public/laravel-cms-uploads'), // maybe changed
        ];
        foreach ($paths as $path) {
            if (file_exists($path)) {
                if (is_dir($path)) {
                    $this->line('<fg=green>**** Delete folder: ' . $path . '</>');
                    \File::deleteDirectory($path);
                } else {
                    $this->line('<fg=green>**** Delete file: ' . $path . '</>');
                    unlink($path);
                }
            }
        }
        $this->line('<fg=green>****</>');
        $this->line('<fg=green>**** Done</>');
        $this->line('<fg=green>****</>');
        $this->line('<fg=cyan>****</>');
        $this->line('<fg=cyan>**** Remove the CMS package ****</>');
        $this->line('<fg=cyan>****</>');
        $this->line('<fg=green>**** Please run below composer command manually</>');
        $this->line('<fg=green>**** composer remove alexstack/laravel-cms</>');
        $this->line('<fg=cyan>****</>');
        $this->line('<fg=cyan>**** Could you tell me why you uninstall it?</>');
        $this->line('<fg=cyan>****</>');
        $this->line('<fg=green>**** Feedback: https://github.com/AlexStack/Laravel-CMS/issues</>');
        $this->line('<fg=green>**** Sorry to see you leave, Have a good day!</>');
        $this->line('<fg=green>****</>');
    }

    public function initializeCms($options)
    {
        $this->line('<fg=red>****</>');
        $this->line('<fg=red>**** Initialize Amila Laravel CMS ****</>');
        $this->line('<fg=red>****</>');

        if (trim($options['table_prefix']) == '') {
            $table_prefix = $this->ask('Set up a database table prefix instead of the default', 'cms_');
        } else {
            $table_prefix = trim($options['table_prefix']);
        }

        if (trim($options['locale']) == '') {
            $app_locale = $this->ask('Set up a locale language instead of the default', config('app.locale'));
        } else {
            $app_locale = trim($options['locale']);
        }


        $this->line("<fg=cyan>----> Database table prefix : </><fg=yellow>" . $table_prefix . "</>");
        $this->line("<fg=cyan>----> Locale language : </><fg=yellow>" . $app_locale . "</>");

        if (trim($options['locale']) == '' || trim($options['table_prefix']) == '') {
            if (!$this->confirm('<fg=magenta>Please confirm the above settings?</>', true)) {
                $this->error("User aborted! please run the command again.");
                exit();
            }
        }

        //exit('This is debug test');

        $this->call('vendor:publish', [
            '--provider' => 'AlexStack\LaravelCms\LaravelCmsServiceProvider'
        ]);

        if ($table_prefix != 'cms_' || $app_locale != 'en') {
            $config_str = str_replace(
                ["=> 'cms_", "=> 'en"],
                ["=> '" . $table_prefix, "=> '" . $app_locale],
                file_get_contents(dirname(__FILE__, 3) . '/config/laravel-cms.php')
            );
            file_put_contents(base_path('config/laravel-cms.php'), $config_str);
        }


        $this->call('config:clear');
        $this->call('route:clear');

        $this->call('migrate', [
            '--path' => './vendor/alexstack/laravel-cms/src/database/migrations/'
        ]);

        $this->call('db:seed', [
            '--class' => 'AlexStack\LaravelCms\CmsSettingsTableSeeder'
        ]);

        $this->call('db:seed', [
            '--class' => 'AlexStack\LaravelCms\CmsPagesTableSeeder'
        ]);

        // success message
        $this->line('<fg=red>****</>');
        $this->line('<fg=red>**** Laravel CMS Initialized ****</>');
        $this->line('<fg=red>****</>');
        $this->line('<fg=cyan>****</>');
        $this->line('<fg=cyan>**** Admin panel: <fg=yellow>' . config('app.url') . '</><fg=magenta>/cmsadmin/</> ****</>');
        $this->line('<fg=cyan>**** Access here: <fg=yellow>' . config('app.url') . '</><fg=magenta>/cmsadmin/</> ****</>');
        $this->line('<fg=cyan>****</>');
        $this->line('<fg=green>****</>');
        $this->line('<fg=green>**** Have a good day!  ****</>');
        $this->line('<fg=green>****</>');
    }
}
