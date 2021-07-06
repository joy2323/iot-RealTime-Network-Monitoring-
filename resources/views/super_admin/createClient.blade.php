@extends('layout.app_super')

@section('title', 'Create Client| SUSEJ IoT- RealTime Network Monitoring')
@section('content')

<section id="middle">

    @if ($message = Session::get('success'))
    <div class="alert alert-success alert-block">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ $message }}</strong>
    </div>
    @endif


    @if ($message = Session::get('error'))
    <div class="alert alert-danger alert-block">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ $message }}</strong>
    </div>
    @endif

    <!-- page title -->
    <header id="page-header">
        <h1>Create Client</h1>
        <ol class="breadcrumb">
            <li><a href="{{url('/clients')}}">Clients</a></li>
            <li class="active">Create Client</li>
        </ol>
    </header>
    <!-- /page title -->

    <div id="content" class="padding-50">

        <div class="row">

            <div class="col">

                <!-- ------ -->
                <div class="panel panel-default">
                    <div class="panel-heading panel-heading-transparent">
                        <strong>Enter Client Info</strong>
                    </div>

                    <div class="panel-body">

                        <form class="" action="/add-client" method="post" enctype="multipart/form-data"
                            data-success="Sent! Thank you!" data-toastr-position="top-right">
                            @csrf
                            <fieldset>
                                <div class="row">
                                    <div class="form-group" style="text-align:center">
                                        <div class="col-md-4">
                                            @if (auth()->user()->image)
                                            <img src="{{ auth()->user()->image }}" id="profile-img-tag" width="200px"
                                                height="200px" style="border-radius: 50%;" />
                                            @endif
                                            <input id="profile_image" type="file" class="form-control"
                                                style="margin-top:20px" name="profile_image" required>
                                        </div>

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-md-12 col-sm-12">
                                            <label> Name *</label>
                                            <input type="text" name="name" required value=""
                                                class="form-control required">
                                        </div>

                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group">

                                        <div class="col-md-12 col-sm-12">
                                            <label>Address *</label>
                                            <input type="text" name="address" required value=""
                                                class="form-control required">
                                        </div>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="form-group">

                                        <div class="col-md-12 col-sm-12">
                                            <label>Contact Number *</label>

                                            <input type="tel" id="number" name="phone_number" onfocus="this.value=''"
                                                placeholder="234-1234567890" pattern="[2-4]{3}-[0-9]{10}" required
                                                class="form-control required">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group">

                                        <div class="col-md-12 col-sm-12">
                                            <label>Dashboard Name Level 1*</label>

                                            <input type="text" id="level1" name="level1" onfocus="this.value=''"
                                                placeholder="Example: Business unit or District" required
                                                class="form-control required">
                                        </div>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="form-group">

                                        <div class="col-md-12 col-sm-12">
                                            <label>Dashboard Name Level 2*</label>

                                            <input type="text" id="level2" name="level2" onfocus="this.value=''"
                                                placeholder="Example: Undertaking or Service Center" required
                                                class="form-control required">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group">
                                        <div class="panel-heading panel-heading-transparent">
                                            <strong>Login Details</strong>
                                        </div>
                                        <div class="col-md-12 col-sm-12">
                                            <label>Login Email *</label>
                                            <input type="text" name="loginemail" required value=""
                                                class="form-control required">
                                        </div>
                                        <div class="col-md-12 col-sm-12">
                                            <label>Password *</label>
                                            <input type="password" name="password" required value=""
                                                class="form-control required">
                                        </div>

                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 col-sm-12 text-right">
                                        <button type="submit" class="btn btn-primary">SAVE</button>
                                        <!-- <i class="fa fa-check"></i>  -->
                                        <!-- SAVE -->
                                        </a>
                                    </div>
                                </div>

                            </fieldset>



                        </form>

                    </div>

                </div>
                <!-- /----- -->

            </div>

        </div>

    </div>

    </div>

    </div>
</section>
<script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery-2.2.3.min.js') }}"></script>
<script type="text/javascript">
const readURL = (input) => {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
            $('#profile-img-tag').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}
$("#profile_image").change(function() {
    readURL(this);
});
</script>

@endsection