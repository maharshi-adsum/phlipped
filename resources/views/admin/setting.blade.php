@extends('admin.layouts.app')
@section("breadcrumb")
<li class="breadcrumb-item active">Users</li>
@endsection
@section("extra_css")
<style>
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type=number] {
        -moz-appearance: textfield;
    }
</style>
@endsection
@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Change Profile</h3>
            </div>
            <div class="card-body">
                <form id="addUpdateAdminProfile" enctype="multipart/form-data" autocomplete="off">

                    {{-- Username --}}
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">
                            Username
                        </label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <input type="text" name="username" class="form-control" value="@if($profile){{$profile->username}}@endif" placeholder="Enter Username">
                            </div>
                        </div>
                    </div>
                    <hr>

                    {{-- Email --}}
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">
                            Email
                        </label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <input type="text" name="email" class="form-control" value="@if($profile){{$profile->email}}@endif" placeholder="Enter Email">
                            </div>
                        </div>
                    </div>
                    <hr>

                    {{-- Profile Image --}}
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">
                            Profile Image
                        </label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input type="file" name="profile_image" id="profile_image" accept="image/*"
                                    class="form-control" placeholder="Enter Profile Image">
                            </div>
                        </div>
                        <div class="form-group ml-3">
                            <img src="@if($profile){{$profile->profile_image}}@else{{asset('public/phlippedlogo.png')}}@endif" class="img-thumbnail image_preview"
                                id="image_preview" style="width: 145px; height: 145px;">
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">
                            Days
                        </label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <input type="number" name="day" class="form-control" value="@if($profile){{$profile->day}}@endif" placeholder="Enter Days">
                            </div>
                        </div>
                    </div>
                    <hr>

                    <div class="pt-2">
                        <button type="submit" class="btn float-right" style="background-color: #0091D6" id="submitProfile">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Change Password</h3>
            </div>
            <div class="card-body">
                <form id="addUpdateAdminPassword" autocomplete="off">

                    {{-- Old Password --}}
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">
                            Old Password
                        </label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <input type="password" name="old_password" class="form-control" id="old_password"
                                    value="" placeholder="Old Password">
                            </div>
                        </div>
                    </div>
                    <hr>

                    {{-- New Password --}}
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">
                            New Password
                        </label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <input type="password" name="new_password" class="form-control" id="new_password" placeholder="New Password">
                            </div>
                        </div>
                    </div>
                    <hr>

                    {{-- Confirm Password --}}
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">
                            Confirm Password
                        </label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <input type="password" name="confirm_new_password" class="form-control" id="confirm_new_password"
                                    value="" placeholder="Confirm Password">
                            </div>
                        </div>
                    </div>
                    <hr>

                    <div class="pt-2">
                        <button type="submit" class="btn float-right" style="background-color: #0091D6" id="submitPassword">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
    //image preview
    var reader1 = new FileReader();
    reader1.onload = function (e) {
        $('.image_preview').attr('src', e.target.result);
    }

    function readURL1(input) {
        if (input.files && input.files[0]) {
            reader1.readAsDataURL(input.files[0]);
        }
    }
    $("#profile_image").change(function () {
        readURL1(this);
    });

    $('#submitProfile').click(function (event) {
        event.preventDefault()
        var myform = document.getElementById("addUpdateAdminProfile");
        var formData = new FormData(myform);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: "{{route('addUpdateAdminProfile')}}",
            data: formData,
            success: function (data) {
                if (data.status == 1) {
                    iziToast.success({
                        title: 'Success!',
                        message: data.messages,
                        position: 'topRight'
                    });
                } else {
                    iziToast.error({
                        title: 'Error!',
                        message: data.messages,
                        position: 'topRight'
                    });
                }
                location.reload();
            },
            error: function (data) {
                iziToast.error({
                    title: 'Error!',
                    message: data.messages,
                    position: 'topRight'
                });
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });

    $('#submitPassword').click(function (event) {
        event.preventDefault()
        var myform = document.getElementById("addUpdateAdminPassword");
        var formData = new FormData(myform);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: "{{route('addUpdateAdminPassword')}}",
            data: formData,
            success: function (data) {
                if (data.status == 1) {
                    $('#old_password').val('');
                    $('#new_password').val('');
                    $('#confirm_new_password').val('');
                    iziToast.success({
                        title: 'Success!',
                        message: data.messages,
                        position: 'topRight'
                    });
                } else {
                    iziToast.error({
                        title: 'Error!',
                        message: data.messages,
                        position: 'topRight'
                    });
                }
            },
            error: function (data) {
                iziToast.error({
                    title: 'Error!',
                    message: data.messages,
                    position: 'topRight'
                });
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });
</script>
@endsection
