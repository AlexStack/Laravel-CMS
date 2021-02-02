<?php

namespace AlexStack\LaravelCms\Console\Commands;

use AlexStack\LaravelCms\Helpers\LaravelCmsHelper;
use Illuminate\Console\Command;

class LaravelCMS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravelcms
            {--a|action=initialize : -a upgrade or -a install or -a uninstall or -a clear}
            {--p|table_prefix= : Default table_prefix is cms_}
            {--l|locale= : Default locale is en}
            {--s|silent=no : Silent mode yes or no}
            {--u|db_username= : username of the database}
            {--w|db_password= : password of the database}
            {--d|db_database= : database name}
            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Artisan commands for Laravel CMS';

    /**
     * Create a new command instance.
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
        if ('initialize' == $options['action'] || 'install' == $options['action']) {
            $this->initializeCms($options);
        } elseif ('uninstall' == $options['action'] || 'remove' == $options['action']) {
            $this->uninstall($options);
        } elseif ('upgrade' == $options['action'] || 'update' == $options['action']) {
            $this->upgrade($options);
        } elseif ('clear' == $options['action'] || 'clean' == $options['action']) {
            $this->clearCache($options);
        } else {
            $this->error('Wrong action');
        }
    }

    public function upgrade($options)
    {
        $this->line('<fg=red>****</>');
        $this->line('<fg=red>**** Upgrade Amila Laravel CMS ****</>');
        $this->line('<fg=red>****</>');

        if ('no' != trim($options['silent']) || $this->confirm('<fg=cyan>**** Upgrade the CMS database tables? ****</>', true)) {
            $this->call('migrate', [
                '--path'  => './vendor/alexstack/laravel-cms/src/database/migrations/',
                '--force' => true,
            ]);
            // other database changes
        }

        // override view & asset files
        if ('no' != trim($options['silent']) || $this->confirm('<fg=cyan>**** Copy the CMS backend & frontend view & asset files? ****</>', true)) {
            // copy frontend & backend view files
            $target_dir   = base_path('resources/views/vendor/laravel-cms');
            $backup_dir   = storage_path('app/laravel-cms/backups/views/templates');
            $ignore_files = ['plugins', 'uploads', 'backups'];
            $source_files = glob(dirname(__FILE__, 3).'/resources/views/*', GLOB_ONLYDIR);

            $this->line('<fg=green>- The exists templates will backup to:</> '.$backup_dir);
            $this->line('<fg=green>- </> ');

            $this->copyCmsFiles($source_files, $target_dir, $backup_dir, $ignore_files);

            // copy plugin view files
            $target_dir   = base_path('resources/views/vendor/laravel-cms/plugins');
            $backup_dir   = storage_path('app/laravel-cms/backups/views/plugins');
            $source_files = glob(dirname(__FILE__, 3).'/resources/views/plugins/*', GLOB_ONLYDIR);
            $ignore_files = ['test', 'backups'];

            $this->line('<fg=green>- </> ');
            $this->line('<fg=green>- The exists plugin views will backup to:</> '.$backup_dir);
            $this->line('<fg=green>- </> ');

            $this->copyCmsFiles($source_files, $target_dir, $backup_dir, $ignore_files);

            $this->line('<fg=green>- </> ');
            $this->line('<fg=green>- Publish new template & plugin views:</> ');
            $this->line('<fg=green>- </> ');

            $this->call('vendor:publish', [
                '--tag'   => 'laravel-cms-views',
                '--force' => 1,
            ]);

            // override asset files
            $target_dir   = public_path('laravel-cms');
            $backup_dir   = storage_path('app/laravel-cms/backups/assets');
            $ignore_files = ['plugins', 'uploads', 'backups'];
            $source_files = glob(dirname(__FILE__, 3).'/assets/*', GLOB_ONLYDIR);

            $this->line('<fg=green>- </> ');
            $this->line('<fg=green>- The exists assets will backup to:</> '.$backup_dir);
            $this->line('<fg=green>- </> ');

            $this->copyCmsFiles($source_files, $target_dir, $backup_dir, $ignore_files);

            $this->line('<fg=green>- </> ');
            $this->line('<fg=green>- Publish new assets:</> ');
            $this->line('<fg=green>- </> ');

            $this->call('vendor:publish', [
                '--tag'   => 'laravel-cms-assets',
                '--force' => 1,
            ]);
        }

        // override lang files
        if ('no' != trim($options['silent']) || $this->confirm('<fg=cyan>**** Copy the CMS backend & frontend language files? ****</>', true)) {
            // move lang files, lang/en lang/zh etc.
            $vendor_lang_path    = dirname(__FILE__, 3).'/resources/lang';
            $target_dir          = base_path('resources/lang/vendor/laravel-cms');
            $backup_dir          = storage_path('app/laravel-cms/backups/lang');
            $source_dirs         = glob($vendor_lang_path.'/*', GLOB_ONLYDIR);

            $this->line('<fg=green>- </> ');
            $this->line('<fg=green>- The exists language files will backup to:</> '.$backup_dir);
            $this->line('<fg=green>- </> ');

            foreach ($source_dirs as $dir) {
                $lang_dir     = basename($dir);
                $source_files = glob($dir.'/*.php');

                $this->copyCmsFiles(
                    $source_files,
                    $target_dir.'/'.$lang_dir,
                    $backup_dir.'/'.$lang_dir
                );
            }

            $this->line('<fg=green>- </> ');
            $this->line('<fg=green>- Publish new language files:</> ');
            $this->line('<fg=green>- </> ');

            $this->call('vendor:publish', [
                '--tag'   => 'laravel-cms-lang',
                '--force' => 1,
            ]);
        }

        $this->clearCache($options);

        // success message
        $this->line('<fg=red>****</>');
        $this->line('<fg=red>**** Laravel CMS Upgraded ****</>');
        $this->line('<fg=red>****</>');
        $this->line('<fg=cyan>****</>');
        $this->line('<fg=cyan>**** Admin panel: <fg=yellow>'.config('app.url').'</><fg=magenta>/cmsadmin/</> ****</>');
        $this->line('<fg=cyan>**** Access here: <fg=yellow>'.config('app.url').'</><fg=magenta>/cmsadmin/</> ****</>');
        $this->line('<fg=cyan>****</>');
        $this->line('<fg=green>****</>');
        $this->line('<fg=green>**** Have a good day!  ****</>');
        $this->line('<fg=green>****</>');
    }

    public function uninstall($options)
    {
        $this->line('<fg=red>****</>');
        $this->line('<fg=red>**** UNINSTALL Amila Laravel CMS ****</>');
        $this->line('<fg=red>****</>');

        if ('no' == trim($options['silent']) && ! $this->confirm('<fg=cyan>**** Remove the CMS database tables? ****</>', true)) {
            $this->error('User aborted! please run the command again.');
            exit();
        }

        $this->call('migrate:reset', [
            '--path'  => './vendor/alexstack/laravel-cms/src/database/migrations/',
            '--force' => true,
        ]);

        if ('no' == trim($options['silent']) && ! $this->confirm('<fg=cyan>**** Remove the CMS folders and files? ****</>', true)) {
            $this->error('User aborted! please run the command again.');
            exit();
        }
        $paths = [
            base_path('resources/langs/vendor/laravel-cms'),
            base_path('resources/lang/vendor/laravel-cms'),
            public_path('laravel-cms'),
            base_path('config/laravel-cms.php'),
            storage_path('app/laravel-cms'),
            storage_path('app/public/laravel-cms-uploads'), // maybe changed
        ];
        foreach ($paths as $path) {
            if (file_exists($path)) {
                if (is_dir($path)) {
                    $this->line('<fg=green>**** Delete folder: '.$path.'</>');
                    \File::deleteDirectory($path);
                } else {
                    $this->line('<fg=green>**** Delete file: '.$path.'</>');
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

        $this->checkDbConnection($options);

        if (file_exists(storage_path('app/laravel-cms'))) {
            $this->line('<fg=cyan>****</>');
            $this->line('<fg=cyan>**** Seems the Laravel CMS already initialized ****</>');
            $this->line('<fg=cyan>****</>');

            $this->line('<fg=green>**** To Upgrade It   : </><fg=yellow>php artisan laravelcms --action=upgrade</>');
            $this->line('<fg=green>**** To Clear Cache  : </><fg=yellow>php artisan laravelcms --action=clear</>');
            $this->line('<fg=green>**** To Uninstall It : </><fg=yellow>php artisan laravelcms --action=uninstall</>');

            return false;
        }

        if ('' == trim($options['table_prefix'])) {
            $table_prefix = $this->ask('Set up cms database table prefix instead of the default', 'cms_');
        } else {
            $table_prefix = trim($options['table_prefix']);
        }

        if ('' == trim($options['locale'])) {
            $app_locale = $this->ask('Set up a locale language instead of English eg. en/zh/es/jp/ko/ar/fr/ru/pt/it', config('app.locale'));
        } else {
            $app_locale = trim($options['locale']);
        }

        $this->line('<fg=cyan>----> Database table prefix : </><fg=yellow>'.$table_prefix.'</>');
        $this->line('<fg=cyan>----> Locale language : </><fg=yellow>'.$app_locale.'</>');

        if ('no' == trim($options['silent'])) {
            if (! $this->confirm('<fg=magenta>Please confirm the above settings?</>', true)) {
                $this->error('User aborted! please run the command again.');
                exit();
            }
        }

        //exit('This is debug test');

        $this->call('vendor:publish', [
            '--provider' => 'AlexStack\LaravelCms\LaravelCmsServiceProvider',
        ]);

        $this->rewriteConfig($table_prefix, $app_locale);

        $this->call('config:cache');
        $this->call('route:clear');

        $this->call('migrate', [
            '--path'  => './vendor/alexstack/laravel-cms/src/database/migrations/',
            '--force' => true,
        ]);

        $this->call('db:seed', [
            '--class' => 'AlexStack\LaravelCms\CmsSettingsTableSeeder',
        ]);

        $this->call('db:seed', [
            '--class' => 'AlexStack\LaravelCms\CmsFilesTableSeeder',
        ]);

        $this->call('db:seed', [
            '--class' => 'AlexStack\LaravelCms\CmsPagesTableSeeder',
        ]);

        $this->call('db:seed', [
            '--class' => 'AlexStack\LaravelCms\CmsInquirySettingsTableSeeder',
        ]);

        $this->forBrandNewProject();

        $this->rewriteConfig($table_prefix, $app_locale); // incase something wrong

        $this->clearCache($options);

        // success message
        $this->line('<fg=red>****</>');
        $this->line('<fg=red>**** Laravel CMS Initialized ****</>');
        $this->line('<fg=red>****</>');
        $this->line('<fg=cyan>****</>');
        $this->line('<fg=cyan>**** Admin panel: <fg=yellow>'.config('app.url').'</><fg=magenta>/cmsadmin/</> ****</>');
        $this->line('<fg=cyan>**** Access here: <fg=yellow>'.config('app.url').'</><fg=magenta>/cmsadmin/</> ****</>');
        $this->line('<fg=cyan>****</>');
        $this->line('<fg=green>****</>');
        $this->line('<fg=green>**** Have a good day!  ****</>');
        $this->line('<fg=green>****</>');
    }

    public function clearCache($options)
    {
        $this->line('<fg=cyan>- </> ');
        $this->line('<fg=cyan>- Clear & cache the config/route/cms setting files</>');
        $this->line('<fg=cyan>- </> ');

        $this->call('config:cache');
        $this->call('route:clear');
        $helper = new LaravelCmsHelper();
        $rs     = $helper->rewriteConfigFile();
        if ($rs) {
            $this->line('<fg=green>Re-create the setting file: storage\app\laravel-cms\settings.php</>');
        }
    }

    public function copyCmsFiles($source_files, $target_dir, $backup_dir, $ignore_files=[])
    {
        if (! file_exists($backup_dir)) {
            mkdir($backup_dir, 0755, true);
        }
        if (! file_exists($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        foreach ($source_files as $dir) {
            $folder_name = basename($dir);
            if (in_array($folder_name, $ignore_files)) {
                continue;
            }
            if (file_exists($target_dir.'/'.$folder_name)) {
                $new_name = $folder_name.'-bak-'.date('Y-m-d-His');
                rename($target_dir.'/'.$folder_name, $backup_dir.'/'.$new_name);
                $this->line('<fg=green>- Backed up file:</> '.$new_name);
            }
            if (is_dir($dir)) {
                \File::copyDirectory($dir, $target_dir.'/'.$folder_name);
            } else {
                copy($dir, $target_dir.'/'.$folder_name);
            }
        }

        return true;
    }

    public function forBrandNewProject()
    {
//         $defRootRoute = <<<EOF
        // Route::get('/', function () {
//     return view('welcome');
        // });
        // EOF;
        $defRootRoute = "Route::get('/', function () {\n    return view('welcome');\n});";
        $webRouteStr  = file_get_contents(base_path('routes/web.php'));
        if (false !== strpos($webRouteStr, trim($defRootRoute))) {
            $webRouteStr = str_replace("::get('/',", "::get('/welcome',", $webRouteStr);
            file_put_contents(base_path('routes/web.php'), $webRouteStr);

            $this->line('<fg=green>- Set route / to the CMS homepage</> ');

            $config_str = str_replace(
                "'/cms-home')",
                "'/')",
                file_get_contents(dirname(__FILE__, 3).'/config/laravel-cms.php')
            );
            file_put_contents(base_path('config/laravel-cms.php'), $config_str);
        } else {
            // $this->line('<fg=green>- old route:</> '. $defRootRoute);
        }
        // add a public/css/app.css for auth/login page
        if (! file_exists(public_path('css'))) {
            $target_dir   = public_path('css');
            $backup_dir   = storage_path('app/laravel-cms/backups/css');
            $ignore_files = [];
            $source_files = glob(dirname(__FILE__, 3).'/assets/publicCss/*');

            // $this->line('<fg=green>- The exists public/css will backup to:</> '.$backup_dir);
            // $this->line('<fg=green>- </> ');

            $this->copyCmsFiles($source_files, $target_dir, $backup_dir, $ignore_files);
        }
    }

    public function rewriteConfig($table_prefix, $app_locale)
    {
        if ('cms_' != $table_prefix || 'en' != $app_locale) {
            $config_str = str_replace(
                ["=> 'cms_", "=> 'en"],
                ["=> '".$table_prefix, "=> '".$app_locale],
                file_get_contents(dirname(__FILE__, 3).'/config/laravel-cms.php')
            );
            $config_str = trim($config_str); // in case it not change in ram memory
            file_put_contents(base_path('config/laravel-cms.php'), $config_str);
            $this->line('<fg=cyan>----> Changed db table prefix to : </><fg=yellow>'.$table_prefix.'</>');
        }
    }

    public function checkDbConnection($options)
    {
        $defDbStr   = "DB_PORT=3306\nDB_DATABASE=laravel\nDB_USERNAME=root\nDB_PASSWORD=\n";
        $envFileStr = file_get_contents(base_path('.env'));
        if (strpos($envFileStr, $defDbStr)) {
            // try to connection db

            $this->line('<fg=yellow>**** Please set up database connection parameters : </>');

            if ('' == trim($options['db_username'])) {
                $db_username = $this->ask('Please enter the username of your database(DB_USERNAME)', 'root');
            } else {
                $db_username = trim($options['db_username']);
            }

            if ('' == trim($options['db_password'])) {
                $db_password = $this->ask('Please enter the password of your database(DB_PASSWORD)', '');
            } else {
                $db_password = trim($options['db_password']);
            }

            if ('' == trim($options['db_database'])) {
                $db_database = $this->ask('Please enter the database name', 'laravelcms');
            } else {
                $db_database = trim($options['db_database']);
            }

            // test connection before write to .env
            try {
                $mysqli = new \mysqli('localhost', $db_username, $db_password, $db_database);
                if ($mysqli->host_info) {
                    $this->line('<fg=yellow>**** Database connected </>');
                    $envFileStr = str_replace($defDbStr, "DB_PORT=3306\nDB_DATABASE=$db_database\nDB_USERNAME=$db_username\nDB_PASSWORD=$db_password\n", $envFileStr);
                    file_put_contents(base_path('.env'), $envFileStr);

                    $this->call('config:clear');
                    $this->call('config:cache');

                    // $this->call('laravelcms');

                    // all above will not load the new .env for the current artisan execution
                    // have to re-run the php artisan laravelcms again  :(

                    $this->line('<fg=red>****</>');
                    $this->line('<fg=red>**** New .env file regenerated, please run "php artisan laravelcms" again ****</>');
                    $this->line('<fg=red>****</>');
                    exit();
                } else {
                    $this->line('<fg=yellow>**** Database connect failed</>');
                }
            } catch (\Exception $e) {
                $this->line('<fg=yellow>**** Database connect failed !!</>Caught exception: '.$e->getMessage().'');
                $this->checkDbConnection($options);
            }
        } else {
            // not the default .env
        }
    }
}
