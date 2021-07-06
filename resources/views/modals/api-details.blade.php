<!-- Modal -->
<div class="modal fade" id="modal-view-api" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"> </h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="" enctype="multipart/form-data" id="view-key-details">
                    @csrf

                    <input type="hidden" name="id" id="data-id" value="">

                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="dataplan">Owner</label>
                            <input type="text" name="name" id="name" class="form-control" disabled>
                        </div>

                        <div class="form-group col-md-12">
                            <label for="">Status </label>
                            <input type="text" name="status" id="status" class="form-control" disabled>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="bonus"> API Key</label>
                            <input name="apikey" id="apikey" class="form-control" disabled>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <button style="float:left" id="keycontrol" data-value="1" class="btn btn-lg btn-info"
                                    type="button">
                                    Deactivate key
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button style="float:right" id="regenkey" type="button"
                                    class="btn btn-lg btn-info">Regenerate
                                    key</button>
                            </div>
                        </div>
                    </div> {{-- Close Modal footer --}}
            </div>

            </form>
        </div> {{-- Modal Body --}}
    </div> {{-- End Modal Dialog --}}
</div> {{-- End Modal --}}