<!-- Modal -->
<div class="modal fade" id="modal-config-site" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"> Site Channel Configuration </h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="" enctype="multipart/form-data" id="edit-site-plan">
                    <input type="hidden" name="_token" value=" {{ csrf_token() }}">
                    <input type="hidden" name="id" id="data-id" value="">

                    <div class="row">
                        <div class="modal-header">
                            <h5 class="modal-title" id="myModalLabel"> <strong>Analog Channels</strong>  </h5>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="price"> Env. Temp.</label>
                            <input id="Etmulti" name="Etmulti" id="data-name" class="form-control" style="margin-bottom:2px">
                            <select id="Et" name="Et" class="form-control select2" style="width:100%;">
                                <option value="1" selected="selected">Enable</option>
                                <option value="0" >Disable</option>
                            </select>

                        </div>
                         <div class="form-group col-md-4">
                            <label for="price">Oil Temp.</label>
                            <input name="oilTMulti" id="oilTMulti" class="form-control" style="margin-bottom:2px">
                            <select id="oilT" name="oilT" class="form-control select2" style="width:100%">
                                <option value="1" selected="selected">Enable</option>
                                <option value="0" >Disable</option>
                            </select>
                        </div>
                         <div class="form-group col-md-4">
                            <label for="bonus"> Oil Level</label>
                            <input name="level" id="level" class="form-control" style="margin-bottom:2px">
                            <select id="oilLevel" name="oilLevel" class="form-control select2" style="width:100%">
                                <option value="1" selected="selected">Enable</option>
                                <option value="0" >Disable</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="bonus">Phase A Voltage</label>
                            <input name="Va" id="Va" class="form-control"  style="margin-bottom:2px">
                            <select id="voltga" name="voltga" class="form-control select2" style="width:100%">
                                <option value="1" selected="selected">Enable</option>
                                <option value="0" >Disable</option>
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="price"> Phase B Voltage</label>
                            <input type="Vb" name="Vb" id="data-name" class="form-control" style="margin-bottom:2px">
                            <select id="voltb" name="voltb" class="form-control select2" style="width:100%">
                                <option value="1" selected="selected">Enable</option>
                                <option value="0" >Disable</option>
                            </select>
                        </div>
                         <div class="form-group col-md-4">
                            <label for="price"> Phase C Voltage</label>
                            <input name="Vc" id="Vc" class="form-control" style="margin-bottom:2px">
                            <select id="voltc" name="voltc" class="form-control select2" style="width:100%">
                                <option value="1" selected="selected">Enable</option>
                                <option value="0" >Disable</option>
                            </select>
                        </div>
                         <div class="form-group col-md-4">
                            <label for="bonus"> Phase A Current</label>
                            <input name="Ca" id="Ca" class="form-control" style="margin-bottom:2px">
                            <select id="currenta" name="currenta" class="form-control select2" style="width:100%">
                                <option value="1" selected="selected">Enable</option>
                                <option value="0" >Disable</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="bonus"> Phase B Current</label>
                            <input name="Cb" id="Cb" class="form-control" style="margin-bottom:2px">
                            <select id="currentb" name="currbnta" class="form-control select2" style="width:100%">
                                <option value="1" selected="selected">Enable</option>
                                <option value="0" >Disable</option>
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="price"> Phase C Current</label>
                            <input id="Cc" name="Cc" id="data-name" class="form-control" style="margin-bottom:2px">
                            <select id="currentc" name="currentc" class="form-control select2" style="width:100%">
                                <option value="1" selected="selected">Enable</option>
                                <option value="0" >Disable</option>
                            </select>
                        </div>

                    </div>

                    <div class="row">
                        <div class="modal-header">
                            <h5 class="modal-title" id="myModalLabel"><strong>Digital Channels</strong></h5>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="price">Upriser 1</label>
                            <select id="statusupA" name="statusupA" class="form-control select2">
                                <option value="1" selected="selected">Enable</option>
                                <option value="0" >Disable</option>
                            </select>
                        </div>
                         <div class="form-group col-md-3">
                            <label for="price">Upriser B</label>
                            <select id="statusupB" name="statusupB" class="form-control select2">
                                <option value="1" selected="selected">Enable</option>
                                <option value="0" >Disable</option>
                            </select>
                        </div>
                         <div class="form-group col-md-3">
                            <label for="price">Upriser 3</label>
                            <select id="statusupC" name="statusupC" class="form-control select2">
                                <option value="1" selected="selected">Enable</option>
                                <option value="0" >Disable</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="price">Upriser 4</label>
                            <select id="statusupD" name="statusupD" class="form-control select2">
                                <option value="1" selected="selected">Enable</option>
                                <option value="0" >Disable</option>
                            </select>
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
