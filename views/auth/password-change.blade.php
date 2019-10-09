@extends('guest.layout',['webpage_title'=>'Home'])

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
        <div class="col-md-4" style="margin:0 auto;">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title" data-i18n="Auth.Password.HeadingForgottenPassword">
                        Change Password
                    </h3>
                </div>
                <div id="FormPasswordChange" class="card-body">
                    <!-- START: Password -->
                    <div class="form-group">
                        <input class="form-control" placeholder="Password" name="Password" type="password" value="" data-i18n="Auth.Login.InputPassword">
                    </div>
                    <!-- END: Password -->

                    <!-- START: Password Confirm -->
                    <div class="form-group">
                        <input class="form-control" placeholder="Confirm Password" name="PasswordConfirm" type="password" value="" data-i18n="Auth.Login.InputPasswordConfirm">
                    </div>
                    <!-- END: Password Confirm -->

                    <button class="btn btn-lg btn-success btn-block" onclick="return formPasswordChangeProcess();">
                        <img data-icon="ionicons ios-checkmark-circle" style="height:36px;color:white;" />
                        &nbsp;
                        <span data-i18n="Auth.PasswordRestore.ButtonRestorePassword">
                            Change Password
                        </span>
                    </button>

                    <div style="margin-top:30px;">
                        <button class="btn btn-info float-left" onclick="$$.to('auth/login');">
                            <img data-icon="ionicons ios-log-in" style="height:24px;color:white;" />
                            &nbsp;
                            <span data-i18n="Auth.Register.ButtonLogin">
                                Login
                            </span>
                        </button>

                        <button class="btn btn-info float-right" onclick="$$.to('auth/register');">
                            <img class="spin" data-icon="ionicons ios-book" style="height:24px;color:white;" />
                            &nbsp;
                            <span data-i18n="Auth.Login.ButtonRegister">
                                Register
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
<script>
    $(function() {
        var token = $.trim($$.getUrlParam('Token'));

        if (token === '') {
            Swal.fire('Error', 'Link  is invalid', 'error');
            setTimeout(function() {
                $$.to('auth/login');
            }, 3000);
        }
    });

    function formPasswordChangeProcess() {
        var token = $.trim($$.getUrlParam('Token'));
        var password = $.trim($('input[name=Password]').val());
        var passwordConfirm = $.trim($('input[name=PasswordConfirm]').val());

        if (password === '') {
            return Swal.fire('Error!', 'Password is required field', 'error');
        }

        if (passwordConfirm === '') {
            return Swal.fire('Error!', 'Confirm Password is required field', 'error');
        }

        if (password != passwordConfirm) {
            return Swal.fire('Error!', 'Password and Confirm Password do not match', 'error');
        }

        var data = {
            "Token": token,
            "Password": password,
            "PasswordConfirm": passwordConfirm
        };
        var cmd = $$.ws("auth/password-change", data);

        cmd.done(function(response) {
            if (response.status !== "success") {
                return Swal.fire('Error!', response.message, 'error');
            }

            Swal.fire('Success', response.message, 'success');

            setTimeout(function() {
                $$.to('auth/login');
            }, 4000);
            return;
        });

        cmd.fail(function(error) {
            return Swal.fire('Error!', 'There was an error. Try again later!', 'error');
        });
    }
</script>
<!-- END: Scripts -->
@endsection