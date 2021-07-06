<div class="modal fade" id="modalpassvalidate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <!--Content-->
        <div class="modal-content">
            <!--Header-->
            <div class="modal-header" style="text-align:center; margin:0px">
                <img src="{{ url('/'.Auth::user()->image)}}" width="100px" height="100px" alt="avatar">
            </div>
            <!--Body-->
            <div class="modal-body text-center" style="margin:0px">
                <h3 style="margin:0px"> {{ Auth::user()->name }}</h3>
                <form method="POST" action="" enctype="multipart/form-data" id="pass_validate" style="margin:0px">
                    <input type="hidden" name="_token" value=" {{ csrf_token() }}">
                    <input type="hidden" name="id" id="data-id" value="">
                    <input type="password" type="text" id="password" name="password" class="form-control required"
                        autocomplete="new-password">
                    <label data-error="wrong" data-success="right" for="password">Enter password to
                        process</label>
                </form>
            </div>
            <div class="text-center" style="padding-bottom:20px">
                <button class="btn btn-success" id="confirmbut" type="submit">Confirm control <i
                        class="fas fa-sign-in-alt  "></i></button>

            </div>
        </div>

    </div>
    <!--/.Content-->
</div>
</div>
<!--Modal: Login with Avatar Form-->
