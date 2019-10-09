<?php

namespace App\Controllers\Guest;

class AuthController
{

    function anyIndex()
    {
        return view('auth.login', get_defined_vars());
    }

    function anyEmailVerify()
    {
        return view('auth.email-verify', get_defined_vars());
    }

    function anyLogin()
    {
        return view('auth.login', get_defined_vars());
    }

    function anyLogout()
    {
        return view('auth.logout', get_defined_vars());
    }

    function anyPasswordChange()
    {
        return view('auth.password-change', get_defined_vars());
    }

    function anyPasswordRestore()
    {
        return view('auth.password-restore', get_defined_vars());
    }

    function anyRegister()
    {
        return view('auth.register', get_defined_vars());
    }
}