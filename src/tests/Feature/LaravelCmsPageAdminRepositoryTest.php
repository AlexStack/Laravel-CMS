<?php

namespace Tests\Repositories;

use AlexStack\LaravelCms\Helpers\LaravelCmsHelper;
use AlexStack\LaravelCms\Models\LaravelCmsPage;
use AlexStack\LaravelCms\Repositories\LaravelCmsPageAdminRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LaravelCmsPageAdminRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var LaravelCmsPageAdminRepository
     */
    protected $laravelCmsPageRepo;

    public function setUp(): void
    {
        parent::setUp();
        // $this->laravelCmsPageRepo = \App::make(LaravelCmsPageAdminRepository::class);
        $this->laravelCmsPageRepo = \App::make(LaravelCmsPageAdminRepository::class);

        $this->laravelCmsPageRepo->setHelper(new LaravelCmsHelper());

        $factory_path = dirname(__FILE__, 3).'/database/factories';
        $this->app->make(\Illuminate\Database\Eloquent\Factory::class)->load($factory_path);
    }

    /**
     * @test create
     */
    public function test_create_LaravelCmsPage()
    {
        $laravelCmsPage = factory(LaravelCmsPage::class)->raw();

        //$laravelCmsPage = factory(LaravelCmsPage::class)->make()->toArray();

        //var_dump($laravelCmsPage);

        $createdLaravelCmsPage = $this->laravelCmsPageRepo->store($laravelCmsPage);

        $createdLaravelCmsPage = $createdLaravelCmsPage->toArray();
        $this->assertArrayHasKey('id', $createdLaravelCmsPage);
        $this->assertNotNull($createdLaravelCmsPage['id'], 'Created LaravelCmsPage must have id specified');
        $this->assertNotNull(LaravelCmsPage::find($createdLaravelCmsPage['id']), 'LaravelCmsPage with given id must be in DB');
        //$this->assertModelData($laravelCmsPage, $createdLaravelCmsPage);
    }

    /**
     * @test read
     */
    public function test_read_LaravelCmsPage()
    {
        $laravelCmsPage = factory(LaravelCmsPage::class)->raw();

        $createdLaravelCmsPage = $this->laravelCmsPageRepo->store($laravelCmsPage);

        $editLaravelCmsPage = $this->laravelCmsPageRepo->edit($createdLaravelCmsPage->id);

        $indexLaravelCmsPage = $this->laravelCmsPageRepo->index();

        //$dbLaravelCmsPage = $dbLaravelCmsPage->toArray();
        //$this->assertModelData($laravelCmsPage->toArray(), $dbLaravelCmsPage);
        $this->assertNotNull($indexLaravelCmsPage['all_pages'], 'index() method of LaravelCmsPage may have error(s)');

        $this->assertNotNull($editLaravelCmsPage['page']->id, 'edit() method of LaravelCmsPage may have error(s)');
    }

    /**
     * @test update
     */
    public function test_update_LaravelCmsPage()
    {
        $laravelCmsPage     = factory(LaravelCmsPage::class)->create();
        $fakeLaravelCmsPage = factory(LaravelCmsPage::class)->raw();

        $updatedLaravelCmsPage = $this->laravelCmsPageRepo->update($fakeLaravelCmsPage, $laravelCmsPage->id);

        //$this->assertModelData($fakeLaravelCmsPage, $updatedLaravelCmsPage->toArray());
        $dbLaravelCmsPage = $this->laravelCmsPageRepo->find($laravelCmsPage->id);
        //$this->assertModelData($fakeLaravelCmsPage, $dbLaravelCmsPage->toArray());

        //var_dump($updatedLaravelCmsPage);
        $this->assertNotNull($updatedLaravelCmsPage, 'Updated LaravelCmsPage must not be null');
    }

    /**
     * @test delete
     */
    public function test_delete_LaravelCmsPage()
    {
        $laravelCmsPage = factory(LaravelCmsPage::class)->create();

        $resp = $this->laravelCmsPageRepo->destroy($laravelCmsPage->id);

        $this->assertTrue($resp);
        $this->assertNull(LaravelCmsPage::find($laravelCmsPage->id), 'LaravelCmsPage should not exist in DB');
    }
}
