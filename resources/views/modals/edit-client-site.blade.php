<!-- Modal -->
<div class="modal fade" id="modal-edit-site" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"> Edit Site Name </h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="" enctype="multipart/form-data" id="edit-site-plan">
                    <input type="hidden" name="_token" value=" {{ csrf_token() }}">
                    <input type="hidden" name="id" id="data-id" value="">

                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="price"> Name</label>
                            <input type="text" name="name" id="data-name" class="form-control">
                        </div>

                    </div>

                    @if(Auth::user()->role =='Super admin')
                    <div class="row">

                        <div class="form-group col-md-12">
                            <label for="price"> Installed SIM Number</label>
                            <input type="tel" id="number" name="number" onfocus="this.value=''"
                                placeholder="234-1234567890" pattern="[2-4]{3}-[0-9]{10}" required
                                class="form-control required">
                        </div>
                    </div>
                    <label for="price"> Control Enable</label>

                    <div class="row">

                        <div class="form-check-inline  col-md-4">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" id="crtl_DE" name="enable" value="0">
                                Disable Control
                            </label>
                        </div>
                        <div class="form-check-inline col-md-4">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" id="crtl_EN1" name="enable" value="2">
                                Enable Main CB
                                Only
                            </label>
                        </div>
                        <div class="form-check-inline col-md-4">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" id="crtl_EN2" name="enable" value="1">
                                Enable All CB
                            </label>
                        </div>

                    </div>

                    @endif
                    <div class="modal-footer">
                        <button class="btn btn-lg btn-info" type="submit">
                            Update
                        </button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div> {{-- Close Modal footer --}}
            </div>

            </form>
        </div> {{-- Modal Body --}}
    </div> {{-- End Modal Dialog --}}
</div> {{-- End Modal --}}