<!-- Modal -->
<div class="modal fade" id="modal-view-site" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Site details </h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="_token" value=" {{ csrf_token() }}">
                <input type="hidden" name="id" id="data-id" value="">

                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="price"> Name</label>
                        <input type="text" name="name" id="data-name" class="form-control">
                    </div>
                    <div class="form-group col-md-12">
                        <label for="price"> Uprisers</label>
                        <input name="uprisers" id="uprisers" class="form-control">
                    </div>
                    <div class="form-group col-md-12">
                        <label for="bonus"> Longitude</label>
                        <input name="long" id="long" class="form-control">
                    </div>
                    <div class="form-group col-md-12">
                        <label for="bonus"> Latitude</label>
                        <input name="lat" id="lat" class="form-control">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div> {{-- Close Modal footer --}}
            </div>
        </div> {{-- Modal Body --}}


    </div> {{-- End Modal Dialog --}}
</div> {{-- End Modal --}}