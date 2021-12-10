@extends('admin.layouts.app')
@section("breadcrumb")
<li class="breadcrumb-item active">Users</li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Users List (<span id="user_count">{{$user_count}}</span>)</h3>
            </div>
            <div class="card-header mb-2">
                <div class="row">
                    <div class="input-group mb-3 col-md-4">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">Search:</span>
                        </div>
                        <input type="text" name="user_search" id="search" class="form-control user_search"
                            placeholder="Search.." autocomplete="off">
                    </div>
                    <div class="col-md-2">
                        <a type="button" class="btn refresh-btn mt-2 mt-md-0" style="font-size: 14px" id="reset"><i
                                class="fa fa-refresh" aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive">
                <table class="table" id="datatable_users">
                    <thead class="thead-inverse">
                        <tr>
                            <th>Sr. No</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">

    $(function () {
        var table = $('#datatable_users').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            searching: false,
            "aaSorting": [
                [0, "desc"]
            ],
            "language": {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw" style="color:#f1c63a;"></i><span class="sr-only"></span> ',
            },
            contentType: 'application/json; charset=utf-8',
            ajax: {
                url: "{{route('usersList')}}",
                type: "POST",
                data: function (d) {
                    d.user_search = $('.user_search').val(),
                        d._token = '{{csrf_token()}}'
                }
            },
            columns: [
                {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'fullname',
                    name: 'fullname'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'country_code_with_phone_number',
                    name: 'country_code_with_phone_number'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $("#search").keyup(function () {
            $('#datatable_users').DataTable().draw(true);
        });
    });
    
    $('#reset').on('click', function () {
        $('#search').val('');
        $('#datatable_users').DataTable().draw(true);
    });

    $(document).on('click', '.delete', function () {
        var id = $(this).attr('data-id');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this user!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: false
        }, function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: "{{route('userDelete')}}",
                data: {
                    id: id
                },
                dataType: 'JSON',
                success: function (data) {
                    swal({
                        title: "Deleted!",
                        text: "User has been deleted.",
                        type: "success"
                    }, function () {
                        $('#datatable_users').DataTable().draw(true);
                    });
                }
            });
        });
    });
</script>
@endsection
