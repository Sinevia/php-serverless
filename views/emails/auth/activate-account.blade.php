@extends('emails.layout', ['title'=>'Verify Email Address'])

@section('content')
<h1 style="font-size:22px;text-align: center;">
    Verify Email Address
</h1>

<p>
    Please click the button below to verify your email address.
</p>

<p>
    <a class="btn-primary" href="<?php echo $activation_link; ?>"
        style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #348eda; margin: 0; border-color: #348eda; border-style: solid; border-width: 10px 20px;">
        Verify Email Address
    </a>
</p>


<p>
    If you did not create an account, no further action is required.
</p>

@endsection