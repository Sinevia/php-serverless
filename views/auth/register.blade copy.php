@extends('guest.layout',['webpage_title'=>'Register'])

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
        <div class="col-sm-12 col-md-6" style="margin:0 auto;">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title" data-i18n="Auth.Login.HeadingSignIn">
                        Please register
                    </h3>
                </div>
                <div class="card-body form_login">
                    <!-- START: Message Area -->
                    <div class="form-group">
                        <div class="alert alert-success" style="display:none;">
                            <a class="close" data-dismiss='alert'>×</a>
                            <strong>Success</strong>
                            <span class="successMessage">&nbsp;</span>
                        </div>
                        <div class="alert alert-danger" style="display:none;">
                            <a class="close" data-dismiss='alert'>×</a>
                            <strong>Error</strong>
                            <span class="errorMessage">&nbsp;</span>
                        </div>
                    </div>
                    <!-- END: Message Area -->

                    <!-- START: First Name -->
                    <div class="form-group">
                        <input class="form-control" placeholder="First Name" name="FirstName" type="text" data-i18n="Auth.Login.InputFirstName" />
                    </div>
                    <!-- END: First Name -->

                    <!-- START: Last Name -->
                    <div class="form-group">
                        <input class="form-control" placeholder="Last Name" name="LastName" type="text" data-i18n="Auth.Login.InputLastName" />
                    </div>
                    <!-- END: Last Name -->

                    <!-- START: Email -->
                    <div class="form-group">
                        <input class="form-control" placeholder="E-mail" name="Email" type="text" data-i18n="Auth.Login.InputEmail" />
                    </div>
                    <!-- END: Email -->

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

                    <input type="hidden" name="sid" value="" />

                    <!-- START: Register -->
                    <button class="buttonLogin btn btn-lg btn-success btn-block" onclick="return registerFormValidate();" data-i18n="Auth.Login.ButtonRegister">
                        <img data-icon="ionicons ios-checkmark-circle" style="height:36px;color:white;" />
                        &nbsp;
                        Register
                        <i class="imgLoading" class="fas fa-spinner fa-spin" style="display:none;"></i>
                    </button>
                    <!-- END: Register -->

                    <!-- START: Actions -->
                    <div style="margin-top:30px;">
                        <button class="btn btn-info float-left" onclick="$$.to('auth/login');">
                            <img data-icon="ionicons ios-log-in" style="height:24px;color:white;" />
                            &nbsp;
                            <span data-i18n="Auth.Register.ButtonLogin">
                                Login
                            </span>
                        </button>
                        <button class="btn btn-warning float-right" onclick="$$.to('auth/password-restore');">
                            <img data-icon="ionicons ios-key" style="height:24px;color:white;" />
                            &nbsp;
                            <span data-i18n="Auth.Register.ButtonForgottenPasssword">
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
    function registerFormValidate() {
        var firstName = $.trim($('input[name=FirstName]').val());
        var lastName = $.trim($('input[name=LastName]').val());
        var email = $.trim($('input[name=Email]').val());
        var password = $.trim($('input[name=Password]').val());
        var passwordConfirm = $.trim($('input[name=PasswordConfirm]').val());

        if (firstName === '') {
            return Swal.fire('Error!', 'First Name is required field', 'error');
        }

        if (lastName === '') {
            return Swal.fire('Error!', 'Last Name is required field', 'error');
        }

        if (email === '') {
            return Swal.fire('Error!', 'Email is required field', 'error');
        }

        if (password === '') {
            return Swal.fire('Error!', 'Password is required field', 'error');
        }

        if (passwordConfirm === '') {
            return Swal.fire('Error!', 'Confirm Password is required field', 'error');
        }

        if (password != passwordConfirm) {
            return Swal.fire('Error!', 'Password and Confirm Password do not match', 'error');
        }



        $('.buttonLogin .imgLoading').show();

        var data = {
            "FirstName": firstName,
            "LastName": lastName,
            "Email": email,
            "Password": password
        };
        var p = $$.ws('auth/register', data);

        p.done(function(response) {
            $('.buttonLogin .imgLoading').hide();

            if (response.status !== "success") {
                return Swal.fire('Error!', response.message, 'error');
            }

            Swal.fire('Success!', response.message, 'success');

            //setTimeout(function () {
            //    $$.to('auth/login');
            //}, 2000);

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