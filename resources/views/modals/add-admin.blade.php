<!-- Modal -->
<div class="modal fade" id="modal-add-admin" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add Admin</h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="" enctype="multipart/form-data" id="create-admin">
                    <input type="hidden" name="_token" value=" {{ csrf_token() }}">

                    <div class="row">
                        <div class="form-group col-md-12">
                            <label>Login Email</label>
                            <input type="text" name="emailaddress" id="emailaddress" required class="form-control">
                        </div>
                        <div class="col-md-12 col-sm-12">
                            <label>Password *</label>
                            <input type="password" name="password" id="password" required value=""
                                class="form-control required">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label> Enable Control</label>
                            <input name="enable" id="enable_ctrl" type="checkbox" value="0">
                        </div>
                        <div class="form-group col-md-6" style="text-align:right">
                            <label>Activate Admin</label>
                            <input name="activate" id="activate_ctrl" type="checkbox" checked value="1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-lg btn-info" type="">
                            Create
                        </button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div> {{-- Close Modal footer --}}
            </div>

            </form>
        </div> {{-- Modal Body --}}
        <!-- <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div> {{-- Close Modal footer --}}
            </div> {{-- End Modal Content --}} -->

    </div> {{-- End Modal Dialog --}}
</div> {{-- End Modal --}}