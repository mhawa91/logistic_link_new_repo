<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Modules\SystemCore\Http\Controllers\SystemCoreHttpController;
use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;

class CrawlingController extends SystemCoreHttpController
{
    public function __construct()
    {
        // dd(33);
    }

    public function dubizzleList()
    {
        $client                             = new Client();
        $crawler                            = $client->request('GET', 'https://verify-sy.com/ar/all/53/%D8%AA%D8%B6%D9%84%D9%8A%D9%84');
        $category_content_pages             = $crawler->filter('.pagination li a')
        ->each(function ($node) {
            return $node->attr('href');
        });
        dd($category_content_pages,$crawler);
    }
    
}
