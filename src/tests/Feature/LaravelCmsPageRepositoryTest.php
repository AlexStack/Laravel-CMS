<?php

namespace Tests\Repositories;

use AlexStack\LaravelCms\Helpers\LaravelCmsHelper;
use AlexStack\LaravelCms\Models\LaravelCmsPage;
use AlexStack\LaravelCms\Repositories\LaravelCmsPageAdminRepository;
use AlexStack\LaravelCms\Repositories\LaravelCmsPageRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LaravelCmsPageRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var LaravelCmsPageRepository
     */
    protected $laravelCmsPageRepo;
    protected $laravelCmsPageAdminRepo;

    public function setUp(): void
    {
        parent::setUp();
        // $this->laravelCmsPageRepo = \App::make(LaravelCmsPageRepository::class);
        $this->laravelCmsPageRepo = \App::make(LaravelCmsPageRepository::class);
        $this->laravelCmsPageRepo->setHelper(new LaravelCmsHelper());

        $this->laravelCmsPageAdminRepo = \App::make(LaravelCmsPageAdminRepository::class);
        $this->laravelCmsPageAdminRepo->setHelper(new LaravelCmsHelper());

        $factory_path = dirname(__FILE__, 3).'/database/factories';
        $this->app->make(\Illuminate\Database\Eloquent\Factory::class)->load($factory_path);
    }

    /**
     * @test read
     */
    public function test_read_LaravelCmsPage()
    {
        $laravelCmsPage = factory(LaravelCmsPage::class)->raw();

        $createdLaravelCmsPage = $this->laravelCmsPageAdminRepo->store($laravelCmsPage);

        //var_dump($createdLaravelCmsPage->toArray());
        $showLaravelCmsPage = $this->laravelCmsPageRepo->show($createdLaravelCmsPage->slug);

        //var_dump($showLaravelCmsPage);
        $this->assertNotNull($showLaravelCmsPage['menus'], 'show() method of frontend LaravelCmsPage may have error(s)');

        $this->assertNotNull($showLaravelCmsPage['plugins'], 'show() method of frontend LaravelCmsPage may have error(s)');
    }

    public function test_homepage_LaravelCmsPage()
    {
        $laravelCmsPage = factory(LaravelCmsPage::class)->raw(['slug' => 'homepage']);

        //var_dump($laravelCmsPage);

        $createdLaravelCmsPage = $this->laravelCmsPageAdminRepo->store($laravelCmsPage);

        //var_dump($createdLaravelCmsPage->toArray());
        $showLaravelCmsPage = $this->laravelCmsPageRepo->show($createdLaravelCmsPage->slug);

        //var_dump($showLaravelCmsPage);
        $this->assertNotNull($showLaravelCmsPage['menus'], 'show() method of frontend LaravelCmsPage may have error(s)');

        $this->assertNotNull($showLaravelCmsPage['plugins'], 'show() method of frontend LaravelCmsPage may have error(s)');
    }
}
