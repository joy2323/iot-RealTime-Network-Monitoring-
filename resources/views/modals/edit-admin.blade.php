<!-- Modal -->
<div class="modal fade" id="modal-edit-admin" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Edit Admin</h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="" enctype="multipart/form-data" id="edit-admin">
                    <input type="hidden" name="_token" value=" {{ csrf_token() }}">
                    <input type="hidden" name="id" id="userid" value=" ">
                    <input type="hidden" name="emailadd" id="emailadd" value=" ">

                    <div class=" row">
                        <div class="form-group col-md-12">
                            <label for="bonus">Login Email</label>
                            <input type="text" name="email" id="email" class="form-control" disabled>
                        </div>
                        <div class="col-md-12 col-sm-12">
                            <label>New Password *</label>
                            <input type="password" name="password" value="" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label> Enable Control</label>
                            <input name="ctrl_auth" id="ctrl_auth" type="checkbox" value="0">
                        </div>
                        <div class="form-group col-md-6" style="text-align:right">
                            <label> Admin Activation</label>
                            <input name="activate_edit" id="activate_edit" type="checkbox" value="1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-lg btn-info" type="submit">
                            Save
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