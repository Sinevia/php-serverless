<?php

namespace App\Controllers\User;

class HomeController extends BaseController
{

    public function anyIndex()
    {
        return $this->anyHome();
    }

    public function anyHome()
    {
        return view('user/home');
    }

    public function anyPage($page = "home")
    {
        $namespace = "user";
        $viewPath = basePath('views/' . $namespace . '/' . $page . '.blade.php');
        if (!\file_exists($viewPath)) {
            if (\Sinevia\Registry::equals('ENVIRONMENT', 'live') == false) {
                return $viewPath . ' page not found';
            }
            return 'Page not found';
        }

        return view($namespace . '/' . $page);
    }

    public function anyModulePage($module, $page = "home")
    {
        if ($module == 'home') {
            return $this->anyPage();
        }
        $namespace = "user";
        $viewPath = basePath('views/' . $namespace . '/' . $module . '/' . $page . '.blade.php');
        if (!\file_exists($viewPath)) {
            if (\Sinevia\Registry::equals('ENVIRONMENT', 'live') == false) {
                return $viewPath . ' page not found';
            }
            return 'Page not found';
        }

        return view($namespace . '/' . $module . '/' . $page);
    }

}
