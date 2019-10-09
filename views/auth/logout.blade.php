@extends('guest.layout',['webpage_title'=>'Logout'])

@section('content')
<!-- START: Message Area -->
<div class="container">
    <div  id="alert-area" class="col-sm-12" style="margin-left: 0px;"></div>
</div>
<!-- END: Message Area -->

<!-- START: Content -->
<div class="container">
    <br />
    <br />

    <div class="row">
        <div class="col-md-12" style="margin:0 auto;">
            <!-- START: Message Area -->
            <div class="form-group">
                <div class="alert alert-success">
                    <a class="close" data-dismiss='alert'>×</a>
                    <strong>Success</strong>
                    <span  class="successMessage">
                        You have been logged out
                    </span>
                </div>
            </div>
            <!-- END: Message Area -->
        </div>
    </div>
</div>
<!-- END: Content -->
@endsection

@section('scripts')
<script>
    $(function(){
        $$.setUser(null);
        $$.setToken(null);
        $$.set('WorkspaceId',null);
        $$.set('Workspaces',null);
        setTimeout(function () {
            $$.to('/');
        }, 3000);
    });
</script>
@endsection
