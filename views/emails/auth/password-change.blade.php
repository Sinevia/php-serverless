@extends('emails.layout', ['title'=>'Password Change Instructions'])

@section('content')
<h1 style="font-size:22px;text-align: center;">
    Reset Password Notification
</h1>

<p>
    You are receiving this email because we received
    a password reset request for your account.
</p>

<p>
    <a class="btn-primary" href="<?php echo $password_change_link; ?>"
        style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #348eda; margin: 0; border-color: #348eda; border-style: solid; border-width: 10px 20px;">
        Reset Password
    </a>
</p>

<p>
    This password reset link will expire in {{ $linkExpiresTime }}.
</p>


<p>
    If you did not request a password reset, no further action is required.
</p>

@endsection