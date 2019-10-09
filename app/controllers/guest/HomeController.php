<?php

namespace App\Controllers\Guest;

class HomeController extends BaseController
{

    function anyIndex()
    {
        return $this->getHome();
    }

    function getHome()
    {
        return view('guest/home');
    }
}