<!-- Modal -->
<div class="modal fade" id="modal-gen-api" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"> </h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="" enctype="multipart/form-data" id="edit-data-plan">
                    @csrf
                    <input type="hidden" name="id" id="data-id" value="">

                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-12 col-sm-12">
                                <select id="client" name="client" class="form-control select2" style="width:100%">
                                    <option value="" selected="selected">Select Client </option>

                                </select>

                            </div>

                        </div>

                        <div class="form-group col-md-12" style="margin-top:20px">
                            <label for="bonus"> API Key</label>
                            <input name="apikey" id="apikey" class="form-control" disabled>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-lg btn-info" id="generate" type="button">
                            Generate key
                        </button>
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