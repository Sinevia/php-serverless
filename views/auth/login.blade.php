@extends('guest.layout',['webpage_title'=>'Login'])

@section('content')
<!-- START: Message Area -->
<div class="container">
    <div id="alert-area" class="col-sm-12" style="margin-left: 0px;"></div>
</div>
<!-- END: Message Area -->

<!-- START: Content -->
<div class="container">
    <br />
    <br />

    <div class="row">
        <div class="col-lg-6 col-md-8 col-sm-12" style="margin:0 auto;">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title" data-i18n="Auth.Login.HeadingSignIn">
                        Please sign in
                    </h3>
                </div>
                <div class="card-body form_login">

                    <!-- START: Email -->
                    <div class="form-group">
                        <input class="form-control" placeholder="E-mail" name="Email" type="text"
                            data-i18n="Auth.Login.InputEmail" />
                    </div>

                    <!-- START: Password -->
                    <div class="form-group">
                        <input class="form-control" placeholder="Password" name="Password" type="password" value=""
                            data-i18n="Auth.Login.InputPassword">
                    </div>
                    <!-- END: Password -->

                    <!-- START: Remember Me -->
                    <div class="checkbox">
                        <label>
                            <input name="remember" type="checkbox" value="Remember Me">
                            <span data-i18n="Auth.Login.LabelRememberMe">Remember Me</span>
                        </label>
                    </div>
                    <!-- END: Remember Me -->

                    <input type="hidden" name="sid" value="" />

                    <!-- START: Login -->
                    <button class="buttonLogin btn btn-lg btn-success btn-block" onclick="return loginFormValidate();"
                        data-i18n="Auth.Login.ButtonLogin">
                        <img data-icon="ionicons ios-checkmark-circle" style="height:36px;color:white;" />
                        &nbsp;
                        Login
                        <i class="imgLoading fas fa-spinner fa-spin" style="display:none;"></i>
                    </button>
                    <!-- END: Login -->

                    <!-- START: Actions -->
                    <div style="margin-top:30px;">
                        <button onclick="$$.to('auth/register');" class="btn btn-info float-left">
                            <img class="spin" data-icon="ionicons ios-book" style="height:24px;color:white;" />
                            &nbsp;&nbsp;
                            <span data-i18n="Auth.Login.ButtonRegister">
                                Register
                            </span>
                        </button>
                        <button onclick="$$.to('auth/password-restore');" class="btn btn-warning float-right">
                            <img data-icon="ionicons ios-key" style="height:24px;color:white;" />
                            &nbsp;

                            <span data-i18n="Auth.Login.ButtonForgottenPasssword">
                                Forgotten Password?
                            </span>
                        </button>
                    </div>
                    <!-- END: Actions -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END: Content -->
@endsection

@section('scripts')
<script>
$(function() {
    if ($$.getUser() !== null) {
        $$.to('user/home.html');
    }
});
</script>
<!-- START: Scripts -->
<script>
/**
 * Validate Login Form
 * @returns {Boolean}
 */
function loginFormValidate() {
    var email = $.trim($('input[name=Email]').val());
    var password = $.trim($('input[name=Password]').val());

    if (email === '') {
        return Swal.fire('Error!', 'Email is required', 'error');
    }

    if (password === '') {
        return Swal.fire('Error!', 'Password is required', 'error');
    }

    $('.buttonLogin .imgLoading').show();

    var data = {
        "Email": email,
        "Password": password
    };
    var p = $$.ws('auth/login', data);

    p.done(function(response) {
        $('.buttonLogin .imgLoading').hide();

        if (response.status !== "success") {
            return Swal.fire('Error!', response.message, 'error');
        }

        $$.setToken(response.data.token);
        $$.setUser(response.data.user);
        Swal.fire('Success!', response.message, 'success');
        setTimeout(function() {
            $$.to('user/home');
        }, 2000);
        return;
    });

    p.fail(function(error) {
        $('.buttonLogin .imgLoading').hide();
        return Swal.fire('Error!', 'There was an error. Try again later!', 'error');
    });
}
$(function() {
    $("#email").focus();
});
</script>
<!-- END: Scripts -->
@endsection
