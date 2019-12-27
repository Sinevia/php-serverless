@extends('guest.layout',['webpage_title'=>'Email Verification'])

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
                        E-mail verification
                    </h3>
                </div>
                <div class="card-body form_login">
                    Please wait we are checking your e-mail
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
 * Verifies e-mail
 * @returns {Boolean}
 */
function verifyEmail() {
    var token = $.trim($$.getUrlParam('Token'));

    if (token === '') {
        return Swal.fire('Error', 'Link  is invalid', 'error');
    }

    var p = $$.ws('auth/email-verify', {
        "token": token
    });

    p.done(function(response) {
        $('.buttonLogin .imgLoading').hide();

        if (response.status !== "success") {
            return Swal.fire('Error', response.message, 'error');
        }

        Swal.fire('Success', response.message, 'success');

        setTimeout(function() {
            $$.to('auth/login'); // Lets login
        }, 2000);

        return;
    });

    p.fail(function(error) {
        $('.buttonLogin .imgLoading').hide();
        return loginFormRaiseError('There was an error. Try again later!');
    });
}
$(function() {
    verifyEmail();
});
</script>
<!-- END: Scripts -->
@endsection