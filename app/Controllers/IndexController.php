<?php

namespace App\Controllers;

class IndexController 
{
    public function index($request, $response)
    {
    	$response->json(['msg' => 'hello, world'])->send();
    }
}