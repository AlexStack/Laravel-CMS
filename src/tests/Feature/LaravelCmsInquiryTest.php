<?php

namespace Tests\Repositories;

use AlexStack\LaravelCms\Helpers\LaravelCmsHelper;
use AlexStack\LaravelCms\Models\LaravelCmsPage;
use AlexStack\LaravelCms\Models\LaravelCmsInquirySetting;
use AlexStack\LaravelCms\Helpers\LaravelCmsPluginInquiry;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LaravelCmsInquiryTest extends TestCase
{
    use DatabaseTransactions;


    protected $inquiryController;

    public function setUp(): void
    {
        parent::setUp();
        $this->inquiryController = \App::make(LaravelCmsPluginInquiry::class);
    }

    public function test_displayForm()
    {
        // enable inquiry form for page id 2
        $inquiry = new LaravelCmsInquirySetting;
        $inquiry->form_enabled = 1;
        $inquiry->page_id = 2;
        $inquiry->save();


        $response = $this->get(config('app.url') . '/cms-2');
        var_dump($response->getContent());

        $response->assertStatus(200, 'displayForm() method of LaravelCmsPluginInquiry may have error(s)');
    }

    public function test_submitForm()
    {
        // enable inquiry form for page id 2
        $inquiry = new LaravelCmsInquirySetting;
        $inquiry->form_enabled = 1;
        $inquiry->page_id = 2;
        $inquiry->save();

        $form_data = [
            'first_name' => 'Name' . rand(0, 999),
            'email' => 'Name' . rand(0, 999) . '@example.com',
            'message' => 'Name' . rand(0, 999),
            'page_id' => 2,
        ];
        $request = new \Illuminate\Http\Request($form_data);


        $rs = json_decode($this->inquiryController->submitForm($request));

        //var_dump($rs);

        $this->assertNotNull($rs->success_content, 'submitForm() method of LaravelCmsPluginInquiry may have error(s)');
    }
}
