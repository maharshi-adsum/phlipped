@extends('admin.layouts.app')
@section("breadcrumb")
<li class="breadcrumb-item active">Buyer Product</li>
@endsection
@section('extra_css')
<style>
.slider .owl-nav button.owl-prev, .slider .owl-nav button.owl-next{
    height: 35px !important;
    width: 35px !important;
    font-size: 30px !important;
    line-height: 30px !important;
}

#buyer_product_description{
    height: 250px;
    overflow: auto;
    border: 1px solid #d8d6d9;
    padding: 5px;
}
</style>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Buyer Product (<span id="user_count">{{$product_all_count}}</span>)</h3>
            </div>
            <div class="card-header mb-2">
                <div class="row">
                    <div class="input-group mb-3 col-md-4">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">Search:</span>
                        </div>
                        <input type="text" name="buyer_product_search" id="search"
                            class="form-control buyer_product_search" placeholder="Search.." autocomplete="off">
                    </div>
                    <div class="col-md-1">
                        <a type="button" class="btn refresh-btn mt-2 mt-md-0" style="font-size: 14px" id="reset"><i
                                class="fa fa-refresh" aria-hidden="true"></i></a>
                    </div>
                    <div class="col-md-5">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="pending-tab" data-toggle="tab" href="#pending" role="tab"
                                    aria-controls="pending" aria-selected="true">Pending (<span
                                        id="pending_count">{{$pending_count}}</span>)</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="approved-tab" data-toggle="tab" href="#approved" role="tab"
                                    aria-controls="approved" aria-selected="false">Approved (<span
                                        id="approved_count">{{$approved_count}}</span>)</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="disapproved-tab" data-toggle="tab" href="#disapproved"
                                    role="tab" aria-controls="disapproved" aria-selected="false">Disapproved (<span
                                        id="disapproved_count">{{$disapproved_count}}</span>)</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                    <div class="card-body table-width table-responsive">
                        <table class="table" id="datatable_buyer_product_pending">
                            <thead>
                                <tr>
                                    <th>Sr. No</th>
                                    <th>Buyer Name</th>
                                    <th>Product Name</th>
                                    <th>Product Image</th>
                                    <th>Created Date</th>
                                    <th>Product View</th>
                                    <th>Approve/Disapprove</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="approved" role="tabpanel" aria-labelledby="approved-tab">
                    <div class="card-body table-width table-responsive">
                        <table class="table" id="datatable_buyer_product_approved">
                            <thead>
                                <tr>
                                    <th>Sr. No</th>
                                    <th>Buyer Name</th>
                                    <th>Product Name</th>
                                    <th>Product Image</th>
                                    <th>Created Date</th>
                                    <th>Product View</th>
                                    <th>Approve</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="disapproved" role="tabpanel" aria-labelledby="disapproved-tab">
                    <div class="card-body table-width table-responsive">
                        <table class="table" id="datatable_buyer_product_disapproved">
                            <thead>
                                <tr>
                                    <th>Sr. No</th>
                                    <th>Buyer Name</th>
                                    <th>Product Name</th>
                                    <th>Product Image</th>
                                    <th>Created Date</th>
                                    <th>Product View</th>
                                    <th>Disapprove</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="buyer_product_name">Product View</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-6">
                         <span>Product Image :</span>
                            <div style="text-align: center;" class="owl-carousel owl-theme slider"
                                id="buyer_product_images">

                            </div>
                        </div>
                        <div class="col-sm-6">
                        <span>Product Description :</span>
                            <p id="buyer_product_description"></p>
                        </div>
                </div>
            </div>
        </div>
        <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>
</div>
@endsection

@section('script')

<script>
    $(".owl-carousel").owlCarousel({
        loop: true,
        nav: true,
        items: 1,
    });

</script>
<script type="text/javascript">

    $(function () {
        var table = $('#datatable_buyer_product_pending').DataTable({
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
                url: "{{route('buyerProductPendingList')}}",
                type: "POST",
                data: function (d) {
                    d.buyer_product_search = $('.buyer_product_search').val(),
                        d._token = '{{csrf_token()}}',
                        d.status = 0
                }
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'fullname',
                    name: 'fullname'
                },
                {
                    data: 'buyer_product_name',
                    name: 'buyer_product_name'
                },
                {
                    data: 'buyer_product_images',
                    name: 'buyer_product_images'
                },
                {
                    data: 'buyer_product_created_at',
                    name: 'buyer_product_created_at'
                },
                {
                    data: 'product_view',
                    name: 'product_view',
                },
                {
                    data: 'approve_and_disapprove_button',
                    name: 'approve_and_disapprove_button'
                }
            ]
        });
    });

    $(function () {
        var table = $('#datatable_buyer_product_approved').DataTable({
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
                url: "{{route('buyerProductPendingList')}}",
                type: "POST",
                data: function (d) {
                    d.buyer_product_search = $('.buyer_product_search').val(),
                        d._token = '{{csrf_token()}}',
                        d.status = 1
                }
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'fullname',
                    name: 'fullname'
                },
                {
                    data: 'buyer_product_name',
                    name: 'buyer_product_name'
                },
                {
                    data: 'buyer_product_images',
                    name: 'buyer_product_images'
                },
                {
                    data: 'buyer_product_created_at',
                    name: 'buyer_product_created_at'
                },
                {
                    data: 'product_view',
                    name: 'product_view',
                },
                {
                    data: 'approve_and_disapprove_button',
                    name: 'approve_and_disapprove_button'
                }
            ]
        });
    });

    $(function () {
        var table = $('#datatable_buyer_product_disapproved').DataTable({
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
                url: "{{route('buyerProductPendingList')}}",
                type: "POST",
                data: function (d) {
                    d.buyer_product_search = $('.buyer_product_search').val(),
                        d._token = '{{csrf_token()}}',
                        d.status = 2
                }
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'fullname',
                    name: 'fullname'
                },
                {
                    data: 'buyer_product_name',
                    name: 'buyer_product_name'
                },
                {
                    data: 'buyer_product_images',
                    name: 'buyer_product_images'
                },
                {
                    data: 'buyer_product_created_at',
                    name: 'buyer_product_created_at'
                },
                {
                    data: 'product_view',
                    name: 'product_view',
                },
                {
                    data: 'approve_and_disapprove_button',
                    name: 'approve_and_disapprove_button'
                }
            ]
        });
    });

    $("#search").keyup(function () {
        $('#datatable_buyer_product_pending').DataTable().draw(true);
        $('#datatable_buyer_product_approved').DataTable().draw(true);
        $('#datatable_buyer_product_disapproved').DataTable().draw(true);
    });

    $('#reset').on('click', function () {
        $('#search').val('');
        $('#datatable_buyer_product_pending').DataTable().draw(true);
        $('#datatable_buyer_product_approved').DataTable().draw(true);
        $('#datatable_buyer_product_disapproved').DataTable().draw(true);
    });

    $(document).on("click", ".view", function () {
        var id = $(this).attr('data-id');
        $.ajax({
            url: "{{route('productView')}}",
            type: "GET",
            data: {
                id: id
            },
            dataType: 'JSON',
            success: function (data) {
                $('#buyer_product_name').html(data.buyer_product_name);
                $('#buyer_product_description').html(data.buyer_product_description);
                $('#buyer_product_images').empty();
                var html =
                    '<div style="text-align: center;" class="owl-carousel owl-theme slider">';
                $.each(data.buyer_product_images, function (key, val) {
                    html += '<img src="' + val +
                        '" class="img-thumbnail w-100" style="height: 250px">'
                });
                html += '</div>';
                $('#buyer_product_images').append(html);
                $(".owl-carousel").owlCarousel({
                    loop: true,
                    nav: true,
                    items: 1,
                    margin:10,
                    autoHeight:true
                });
                $('#viewModal').modal("show");
            }
        });
    });

    $(document).on('click', '.approve', function () {
        var id = $(this).attr('data-id');
        var status = 1;
        swal({
            title: "Are you sure?",
            text: "Are you sure you want to approve this record",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#21bc6c",
            confirmButtonText: "Yes, approve it!",
            closeOnConfirm: false
        }, function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: "{{route('productApproveDisapprove')}}",
                data: {
                    id: id,
                    status: status
                },
                dataType: 'JSON',
                success: function (data) {
                    swal({
                        title: "Approved!",
                        text: "Product has been approved.",
                        type: "success"
                    }, function () {
                        count(data);
                    });
                }
            });
        });
    });

    $(document).on('click', '.disapprove', function () {
        var id = $(this).attr('data-id');
        var status = 2;
        swal({
            title: "Are you sure?",
            text: "Are you sure you want to disapprove this record",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, disapprove it!",
            closeOnConfirm: false
        }, function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: "{{route('productApproveDisapprove')}}",
                data: {
                    id: id,
                    status: status
                },
                dataType: 'JSON',
                success: function (data) {
                    swal({
                        title: "Disapproved!",
                        text: "Product has been disapproved.",
                        type: "success"
                    }, function () {
                        count(data);
                    });
                }
            });
        });
    });

    function count(data)
    {
        document.getElementById("pending_count").innerHTML = data.pending_count;
        document.getElementById("approved_count").innerHTML = data.approved_count;
        document.getElementById("disapproved_count").innerHTML = data.disapproved_count;
        $('#datatable_buyer_product_pending').DataTable().draw(true);
        $('#datatable_buyer_product_approved').DataTable().draw(true);
        $('#datatable_buyer_product_disapproved').DataTable().draw(true);
    }

</script>
@endsection
