<!-- Modal -->
<div class="modal fade" id="modal-edit-siteuser" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"> </h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="" enctype="multipart/form-data" id="edit-siteuser-plan">
                    <input type="hidden" name="_token" value=" {{ csrf_token() }}">
                    <input type="hidden" name="id" id="data-id" value="">

                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="dataplan">Name</label>
                            <input type="text" name="name" id="data-name" class="form-control">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="price">Email</label>
                            <input type="text" name="email" id="data-email" class="form-control">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="price"> Phone Number</label>
                            <input type="tel" id="phone_number" name="phone_number" onfocus="this.value=''"
                                placeholder="234-1234567890" pattern="[2-4]{3}-[0-9]{10}" required
                                class="form-control required">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="bonus"> Address</label>
                            <input name="address" id="address" class="form-control">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-lg btn-info" type="submit">
                            Update
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