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
                        <a href="#" style="color: black">
                            <div class="info-box">
                                <span class="info-box-icon" style="background-color: #0091D6;"><i
                                        class="fas fa-external-link-square-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">User Listing</span>
                                    <span class="info-box-number">0</span>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-lg-3 col-xs-6">
                        <a href="#" style="color: black">
                            <div class="info-box">
                                <span class="info-box-icon" style="background-color: #0091D6;"><i
                                        class="fas fa-exchange-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Buyer listings</span>
                                    <span class="info-box-number">0</span>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-lg-3 col-xs-6">
                        <a href="#" style="color: black">
                            <div class="info-box">
                                <span class="info-box-icon" style="background-color: #0091D6;"><i
                                        class="fas fa-file-invoice"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Seller Quote</span>
                                    <span class="info-box-number">0</span>
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
