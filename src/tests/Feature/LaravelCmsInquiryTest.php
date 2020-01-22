<?php

namespace Tests\Repositories;

use AlexStack\LaravelCms\Helpers\LaravelCmsPluginInquiry;
use AlexStack\LaravelCms\Models\LaravelCmsInquirySetting;
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
        $inquiry               = new LaravelCmsInquirySetting();
        $inquiry->form_enabled = 1;
        $inquiry->page_id      = 2;
        $inquiry->save();

        $response = $this->get(config('app.url').'/cms-2');
        //var_dump($response->getContent());

        $response->assertStatus(200, 'displayForm() method of LaravelCmsPluginInquiry may have error(s)');
    }

    public function test_submitForm()
    {
        // enable inquiry form for page id 2
        $inquiry               = new LaravelCmsInquirySetting();
        $inquiry->form_enabled = 1;
        $inquiry->page_id      = 2;
        $inquiry->save();

        $form_data = [
            'first_name'         => 'Name'.rand(0, 999),
            'email'              => 'email'.rand(0, 999).'@example.com',
            'message'            => 'Message '.rand(0, 999),
            'page_id'            => 2,
            'inquiry_verify_str' => '2-8-9',
        ];
        $request = new \Illuminate\Http\Request($form_data);

        $rs = json_decode($this->inquiryController->submitForm($request));

        //var_dump($rs);

        $this->assertNotNull($rs->success_content, 'submitForm() method of LaravelCmsPluginInquiry may have error(s)');
    }

    public function test_searchResult()
    {
        $response = $this->get(config('app.url').'/cms-Search-CMS.html?keyword=a');
        //var_dump($response->getContent());

        $response->assertStatus(200, 'Search-CMS may have error(s)');
    }

    public function test_redirectLink()
    {
        $response = $this->get(config('app.url').'/cms-redirect-link?url=https://www.laravelcms.tech/');
        //var_dump($response->getContent());

        $response->assertStatus(301, 'redirectLink may have error(s)');
    }
}
