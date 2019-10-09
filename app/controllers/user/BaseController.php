<?php

namespace App\Controllers\User;

class BaseController
{

    /**
     * The current lgged in user
     * @var \App\Models\User
     */
    public $user = null;

    function __construct()
    { }
}