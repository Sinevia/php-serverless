<?php

namespace App\Controllers\Api;

class HomeController extends BaseController {

    function anyIndex() {
        return $this->success('Api is working', ['time' => date('Y-m-d H:i:s')]);
    }

}
