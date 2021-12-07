@extends('admin.layouts.app')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Dashboard</h3>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-lg-3 col-xs-6">
                        <a href="{{route('listUsersIndex')}}" style="color: black">
                            <div class="info-box">
                                <span class="info-box-icon" style="background-color: #0091D6;"><i
                                        class="fa fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">User Listing</span>
                                    <span class="info-box-number">{{$user_count}}</span>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-xs-6">
                        <a href="{{route('buyerProductIndex')}}" style="color: black">
                            <div class="info-box">
                                <span class="info-box-icon" style="background-color: #0091D6;"><i
                                        class="far fa-list-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Buyer Product Listing</span>
                                    <span class="info-box-number">{{$buyer_product_count}}</span>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-xs-6">
                        <a href="{{route('sellerProductIndex')}}" style="color: black">
                            <div class="info-box">
                                <span class="info-box-icon" style="background-color: #0091D6;"><i
                                        class="far fa-list-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Seller Product Listing</span>
                                    <span class="info-box-number">{{$seller_product_count}}</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
