@extends('layout.app_ut')

@section('title', 'Create SiteUser| SUSEJ IoT- RealTime Network Monitoring')
@section('content')


<section id="middle">
    @if(session('status'))
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert">x</button>
        <strong style="text-decoration: white;">{{ session('status') }}</strong>
    </div>
    @endif

    <!-- page title -->
    <header id="page-header">
        <h1>Create SiteUser Admin</h1>
        <ol class="breadcrumb">
            <li><a href="#">SiteUsers</a></li>
            <li class="active">Create SiteUser Admin</li>
        </ol>
    </header>
    <!-- /page title -->
    <div id="content" class="padding-50">

        <div class="row">

            <div class="col">

                <!-- ------ -->
                <div class="panel panel-default">
                    <div class="panel-heading panel-heading-transparent">
                        <strong>Enter SiteUser Admin Info</strong>
                    </div>

                    <div class="panel-body">

                        <form class="" action="/add-siteuser" method="post" enctype="multipart/form-data"
                            data-success="Sent! Thank you!" data-toastr-position="top-right">
                            @csrf
                            <fieldset>

                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-md-12 col-sm-12">
                                            <label> Name *</label>
                                            <input type="text" name="name" value="" class="form-control required">
                                        </div>
                                        <div class="col-md-12 col-sm-12">
                                            <label>Email * <span style="font-size:10px">Enter offical email for
                                                    communication purpose</span> </label>
                                            <input type="email" name="email" value="" class="form-control required">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-md-12 col-sm-12">
                                            <label>Phone Number * <span style="font-size:10px">Enter offical phone
                                                    number for communication purpose</span></label>
                                            <input type="tel" id="phone_number" name="phone_number"
                                                onfocus="this.value=''" placeholder="234-1234567890"
                                                pattern="[2-4]{3}-[0-9]{10}" required class="form-control required">
                                        </div>
                                        <div class="col-md-12 col-sm-12">
                                            <label>Address *</label>
                                            <input type="text" name="address" value="" class="form-control required">
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
                                            <input type="text" name="loginemail" value="" class="form-control required">
                                        </div>
                                        <div class="col-md-12 col-sm-12">
                                            <label>Password *</label>
                                            <input type="password" name="password" value=""
                                                class="form-control required">
                                        </div>
                                        <!-- <div class="col-md-12 col-sm-12">
                                                <label>Start Date *</label>
                                                <input type="text" name="contact[start_date]" value="" class="form-control datepicker required" data-format="yyyy-mm-dd" data-lang="en" data-RTL="false">
                                            </div> -->
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


@endsection