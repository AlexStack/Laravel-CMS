<?php

namespace Tests\Repositories;

use AlexStack\LaravelCms\Helpers\LaravelCmsHelper;
use AlexStack\LaravelCms\Models\LaravelCmsSetting;
use AlexStack\LaravelCms\Repositories\LaravelCmsSettingAdminRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LaravelCmsSettingAdminRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var LaravelCmsSettingAdminRepository
     */
    protected $laravelCmsSettingRepo;

    public function setUp(): void
    {
        parent::setUp();
        // $this->laravelCmsSettingRepo = \App::make(LaravelCmsSettingAdminRepository::class);
        $this->laravelCmsSettingRepo = \App::make(LaravelCmsSettingAdminRepository::class);

        $this->laravelCmsSettingRepo->setHelper(new LaravelCmsHelper());

        $factory_path = dirname(__FILE__, 3).'/database/factories';
        $this->app->make(\Illuminate\Database\Eloquent\Factory::class)->load($factory_path);
    }

    /**
     * @test create
     */
    public function test_create_LaravelCmsSetting()
    {
        $laravelCmsSetting = factory(LaravelCmsSetting::class)->raw();

        //$laravelCmsSetting = factory(LaravelCmsSetting::class)->make()->toArray();

        //var_dump($laravelCmsSetting);

        $createdLaravelCmsSetting = $this->laravelCmsSettingRepo->store($laravelCmsSetting);
        $createdLaravelCmsSetting = $createdLaravelCmsSetting->toArray();
        $this->assertArrayHasKey('id', $createdLaravelCmsSetting);
        $this->assertNotNull($createdLaravelCmsSetting['id'], 'Created LaravelCmsSetting must have id specified');
        $this->assertNotNull(LaravelCmsSetting::find($createdLaravelCmsSetting['id']), 'LaravelCmsSetting with given id must be in DB');
        //$this->assertModelData($laravelCmsSetting, $createdLaravelCmsSetting);
    }

    /**
     * @test read
     */
    public function test_read_LaravelCmsSetting()
    {
        $laravelCmsSetting = factory(LaravelCmsSetting::class)->raw();

        $createdLaravelCmsSetting = $this->laravelCmsSettingRepo->store($laravelCmsSetting);
        $editLaravelCmsSetting    = $this->laravelCmsSettingRepo->edit($createdLaravelCmsSetting->id);
        $indexLaravelCmsSetting   = $this->laravelCmsSettingRepo->index();

        //$dbLaravelCmsSetting = $dbLaravelCmsSetting->toArray();
        //$this->assertModelData($laravelCmsSetting->toArray(), $dbLaravelCmsSetting);
        $this->assertNotNull($indexLaravelCmsSetting['categories'], 'index() method of LaravelCmsSetting may have error(s)');

        $this->assertNotNull($editLaravelCmsSetting['setting']->id, 'edit() method of LaravelCmsSetting may have error(s)');
    }

    /**
     * @test update
     */
    public function test_update_LaravelCmsSetting()
    {
        $laravelCmsSetting     = factory(LaravelCmsSetting::class)->create();
        $fakeLaravelCmsSetting = factory(LaravelCmsSetting::class)->raw();

        $updatedLaravelCmsSetting = $this->laravelCmsSettingRepo->update($fakeLaravelCmsSetting, $laravelCmsSetting->id);

        //$this->assertModelData($fakeLaravelCmsSetting, $updatedLaravelCmsSetting->toArray());
        $dbLaravelCmsSetting = $this->laravelCmsSettingRepo->find($laravelCmsSetting->id);
        //$this->assertModelData($fakeLaravelCmsSetting, $dbLaravelCmsSetting->toArray());

        //var_dump($updatedLaravelCmsSetting);
        $this->assertNotNull($updatedLaravelCmsSetting, 'Updated LaravelCmsSetting must not be null');
    }

    /**
     * @test delete
     */
    public function test_delete_LaravelCmsSetting()
    {
        $laravelCmsSetting = factory(LaravelCmsSetting::class)->create();

        $resp = $this->laravelCmsSettingRepo->destroy($laravelCmsSetting->id);

        $this->assertTrue($resp);
        $this->assertNull(LaravelCmsSetting::find($laravelCmsSetting->id), 'LaravelCmsSetting should not exist in DB');
    }
}
