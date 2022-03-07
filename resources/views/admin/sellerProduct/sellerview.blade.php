@extends('admin.layouts.app')
@section("breadcrumb")
<li class="breadcrumb-item active"> <a href="{{route('sellerProductIndex')}}">Seller Product</a></li>
<li class="breadcrumb-item active">Seller Product View</li>
@endsection
@section('content')
<div class="row">
<div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Buyer Product Details</h3>
            </div>
            <div class="card-body">
                {{-- <span class="font-weight-bold">Product Image :</span> --}}
                <div style="text-align: center;" class="owl-carousel owl-theme slider">
                    @foreach ($data->buyer_product_images as $buyer_product_images)
                    <img src="{{$buyer_product_images}}" class="img-thumbnail w-100" style="height: 320px">
                    @endforeach
                </div>
                <hr>
                <div class="col-sm-12">
                    <span class="font-weight-bold">Name :</span>
                    <span>{{$data->buyerproduct->buyer_product_name}}</span>
                </div>
                <div class="col-sm-12">
                    <span class="font-weight-bold">Description :</span>
                    <span>{{$data->buyerproduct->buyer_product_description}}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-6">
                        <h3 class="card-title">Seller Product Details</h3>
                    </div>
                    <div class="col-6">
                        @if($data->seller_product_status == 0)
                            <a href="javascript:void(0)" id="disapproved" class="disapprove btn btn-danger btn-sm mt-2 mt-md-0" data-id="{{$data->id}}" style="float: right"><span class="fas fa-thumbs-down"></span></a><a href="javascript:void(0)" id="approved" class="approve btn btn-success btn-sm mt-2 mr-2 mt-md-0" data-id="{{$data->id}}" style="float: right"><span class="fas fa-thumbs-up"></span></a>
                        @elseif($data->seller_product_status == 1)
                            <a href="javascript:void(0)" class="btn btn-success btn-sm mt-2 mr-2 mt-md-0" data-id="{{$data->id}}" style="float: right"><span class="fas fa-thumbs-up"></span></a>
                        @elseif($data->seller_product_status == 2)
                            <a href="javascript:void(0)" class="btn btn-danger btn-sm mt-2 mt-md-0" data-id="{{$data->id}}" style="float: right"><span class="fas fa-thumbs-down"></span></a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                {{-- <span class="font-weight-bold">Product Image :</span> --}}
                <div style="text-align: center;" class="owl-carousel owl-theme slider">
                    @foreach ($data->seller_product_images as $seller_product_images)
                    <img src="{{$seller_product_images}}" class="img-thumbnail w-100" style="height: 320px">
                    @endforeach
                </div>
                <hr>
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-6">
                            <span class="font-weight-bold">Name :</span>
                            <span>{{$data->seller_product_name}}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="font-weight-bold">Price :</span>
                            <span>{{$data->seller_product_price}}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="font-weight-bold">location :</span>
                            <span>{{$data->seller_product_location}}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="font-weight-bold">Shipping Charges :</span>
                            <span>{{$data->seller_product_shipping_charges}}</span>
                        </div>
                        <div class="col-sm-12">
                            <span class="font-weight-bold">Condition :</span>
                            <span>{{$data->seller_product_condition}}</span>
                        </div>
                        <div class="col-sm-12">
                            <span class="font-weight-bold">Description :</span>
                            <span>{{$data->seller_product_description}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
    $(".owl-carousel").owlCarousel({
        loop: true,
        nav: true,
        items: 1,
        margin: 10,
        autoHeight: true
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
                url: "{{route('sellerproductApproveDisapprove')}}",
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
                        $('#disapproved').remove();
                        document.getElementById("approved").classList.remove("approve");
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
                url: "{{route('sellerproductApproveDisapprove')}}",
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
                        $('#approved').remove();
                        document.getElementById("disapproved").classList.remove("disapprove");
                    });
                }
            });
        });
    });

</script>
@endsection
