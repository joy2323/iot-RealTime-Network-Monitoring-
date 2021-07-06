<!-- Modal -->
<div class="modal fade" id="modal-edit-profile" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Account Setting</h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="" enctype="multipart/form-data" id="edit-account-password">
                    <input type="hidden" name="_token" value=" {{ csrf_token() }}">
                    <input type="hidden" name="id" id="data-id" value="">
                    <input type="hidden" name="name" id="nameid" value="">
                    <div class="row">
                        <div class="form-group" style="text-align:center">
                            <div class="col-md-6">
                                @if (auth()->user()->image)
                                <img src="{{asset(auth()->user()->image) }}" id="profile-img-tag" width="150px"
                                    height="150px" style="border-radius: 50%;" />
                                @endif
                                <!-- <input id="profile_image" type="file" class="form-control" style="margin-top:20px"
                                    name="profile_image"> -->
                            </div>

                        </div>
                    </div>

                    <div class=" row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="form-group">
                                    <div class="panel-heading panel-heading-transparent">
                                        <strong>Basic Details</strong>
                                    </div>
                                    <div class="col-md-12 col-sm-12">
                                        <label>Name</label>
                                        <input type="text" disabled id="name" value="" class="form-control required">
                                    </div>
                                    <div class="col-md-12 col-sm-12">
                                        <label>Address</label>
                                        <input type="address" name="address" id="address" value=""
                                            class="form-control required">

                                    </div>

                                    <div class="col-md-12 col-sm-12">
                                        <label>Contact Number</label>

                                        <input type="tel" id="number" name="number" onfocus="this.value=''"
                                            placeholder="234-1234567890" pattern="[2-4]{3}-[0-9]{10}" required
                                            class="form-control required">

                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <div class="form-group">
                                    <div class="panel-heading panel-heading-transparent">
                                        <strong>Update Login Password</strong>
                                    </div>
                                    <div class="col-md-12 col-sm-12">
                                        <label>Login Email *</label>
                                        <input type="text" name="email" id="email" disabled required value=""
                                            class="form-control required">
                                    </div>
                                    <div class="col-md-12 col-sm-12">
                                        <label>Password *</label>
                                        <input type="password" name="newpassword" id="newpassReset" value=""
                                            class="form-control required">

                                    </div>
                                    <div class="col-md-12 col-sm-12">
                                        <label>Confirm Password *</label>
                                        <input type="password" name="confirmpassword" id="confirmpassword" value=""
                                            class="form-control required">

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-lg btn-info" type="submit">
                            Update
                        </button>
                        <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">Close</button>
                    </div> {{-- Close Modal footer --}}


                </form>
            </div> {{-- Modal Body --}}
            <!-- <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div> {{-- Close Modal footer --}}
            </div> {{-- End Modal Content --}} -->

        </div> {{-- End Modal Dialog --}}
    </div> {{-- End Modal --}}

    <script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery-2.2.3.min.js') }}"></script>
    <script type="text/javascript">
    $(document).ready(function(e) {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            }
        });
        $("body").on("click", ".account", function() {
            $("#modal-edit-profile").modal('show');
            var userId = $(e.relatedTarget).data('id');
            $.ajax({
                method: 'GET',
                url: '/password/' + userId,
                success: function(data) {
                    $('.modal-body  #newpassReset').val(data.newpassword);
                    $('.modal-body  #name').val(data.name);
                    $('.modal-body  #nameid').val(data.name);
                    $('.modal-body  #email').val(data.email);
                    $('.modal-body  #number').val("234-" + data.phone_number);
                    $('.modal-body  #address').val(data.address);
                    $('#profile-img-tag').attr('src', data.image);
                    $('.modal-body  #data-id').val(data.id);

                }
            });
        });


        $('#edit-account-password').submit(function(e) {
            e.preventDefault();
            let form = $('#edit-account-password').serialize();
            $.ajax({

                url: "{{url('/password-update')}}",
                type: 'POST',
                // data: {
                //     "_method": 'POST',
                //     "id": $('input[name=id]').val(),
                //     "number": $('input[name=number]').val(),
                //     "address": $('input[name=address]').val(),
                //     "name": $('input[name=name]').val(),
                //     "confirmpassword": $('input[name=confirmpassword]').val(),
                //     "profile_image": $('input[name=profile_image]').val(),
                //     "newpassword": $('input[name=newpassword]').val()
                // },
                data: form,

                success: function(response) {
                    if (response.status == 200) {
                        toastr.success('Update successfully', {
                            timeOut: 5000
                        });
                    } else {
                        toastr.warning('Update Fail', {
                            timeOut: 5000
                        });
                    }
                    $("#modal-edit-profile").modal('hide')
                }
            });
        });

        const readURL = (input) => {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#profile-img-tag').attr('src', e.target.result);
                    console.log(e.target.result);
                }
                reader.readAsDataURL(input.files[0]);

            }
        }
        $("#profile_image").change(function() {
            readURL(this);
        });

    });
    </script>