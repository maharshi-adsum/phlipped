@extends('admin.layouts.app')
@section("breadcrumb")
<li class="breadcrumb-item active">Seller Product View
</li>
@endsection
@section('content')
<div class="row">
<div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Buyer Product Detail</h3>
            </div>
            <div class="card-body">
                    <span class="font-weight-bold">Product Image :</span>
                    <div style="text-align: center;" class="owl-carousel owl-theme slider">
                        @foreach ($data->buyer_product_images as $buyer_product_images)
                        <img src="{{$buyer_product_images}}" class="img-thumbnail w-100" style="height: 320px">
                        @endforeach
                    </div>
                <hr>
                <div class="col-sm-12">
                            <span class="font-weight-bold">Product Name :</span>
                            <span>{{$data->buyerproduct->buyer_product_name}}</span>
                        </div>
                        <div class="col-sm-12">
                            <span class="font-weight-bold">Product Description :</span>
                            <span>{{$data->buyerproduct->buyer_product_description}}</span>
                        </div>
            </div>
        </div>
    </div>


    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Seller Product Detail</h3>
            </div>
            <div class="card-body">
                    <span class="font-weight-bold">Product Image :</span>
                    <div style="text-align: center;" class="owl-carousel owl-theme slider">
                        @foreach ($data->seller_product_images as $seller_product_images)
                        <img src="{{$seller_product_images}}" class="img-thumbnail w-100" style="height: 320px">
                        @endforeach
                    </div>
                <hr>
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-6">
                            <span class="font-weight-bold">Product Name :</span>
                            <span>{{$data->seller_product_name}}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="font-weight-bold">Product Price :</span>
                            <span>{{$data->seller_product_price}}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="font-weight-bold">Product location :</span>
                            <span>{{$data->seller_product_location}}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="font-weight-bold">Product Shipping Charges :</span>
                            <span>{{$data->seller_product_shipping_charges}}</span>
                        </div>
                        <div class="col-sm-12">
                            <span class="font-weight-bold">Product Condition :</span>
                            <span>{{$data->seller_product_condition}}</span>
                        </div>
                        <div class="col-sm-12">
                            <span class="font-weight-bold">Product Description :</span>
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

</script>
@endsection
