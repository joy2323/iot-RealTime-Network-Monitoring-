<!-- Modal -->
<div class="modal fade" id="modal-edit-communication" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"> Edit Communication Info </h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="" enctype="multipart/form-data" id="edit-communication-plan">
                    <input type="hidden" name="_token" value=" {{ csrf_token() }}">
                    <input type="hidden" name="id" id="data-id" value="role">

                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="price"> Email Address</label>
                            <input type="text" onfocus="this.value=''" name="email_address" id="email_address"
                                class="form-control">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="price"> Phone Number</label>
                            <input type="tel" id="sms_mobile_number" name="phone_number" onfocus="this.value=''"
                                placeholder="234-1234567890" pattern="[2-4]{3}-[0-9]{10}" required
                                class="form-control required">
                        </div>

                        <div class="form-group col-md-12">

                            @if(Auth::user()->role =='Super admin')
                            <label style="font-weight: bold;"> Communication Enable</label><br>
                            <label for="">Email </label> <input style="margin-right:50px" name="enable" id="enable"
                                type="checkbox" value="">
                            <label for="">SMS </label> <input name="enablesms" id="enabletext" type="checkbox" value="">
                            @else
                            <label style="font-weight: bold;"> Communication Enable</label>
                            <input name="enable" id="enable" type="checkbox" value="">
                            @endif
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