<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;
use App\Traits\UtilityTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\BuyerProducts;
use App\Models\SellerProducts;
use App\Models\Admin;
use Auth;
use Carbon\Carbon;
use App\Models\Wishlist;
use App\Models\User;
use App\Models\Payment;

class CommonController extends Controller
{
    use ResponseTrait, UtilityTrait;

    /**
     * Swagger defination got one all product
     *
     * @OA\Post(
     *     tags={"Got One Product"},
     *     path="/gotOneAllProduct",
     *     description="got one all product",
     *     summary="got one all product",
     *     operationId="gotOneAllProduct",
     * @OA\Parameter(
     *     name="Content-Language",
     *     in="header",
     *     description="Content-Language",
     *     required=false,@OA\Schema(type="string")
     *     ),
     * @OA\RequestBody(
     *     required=true,
     * @OA\MediaType(
     *     mediaType="multipart/form-data",
     * @OA\JsonContent(
     * @OA\Property(
     *     property="user_id",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="page",
     *     description="Page Number",
     *     type="integer"
     *     ),
     *    )
     *   ),
     *  ),
     * @OA\Response(
     *     response=200,
     *     description="User response",@OA\JsonContent
     *     (ref="#/components/schemas/SuccessResponse")
     * ),
     * @OA\Response(
     *     response="400",
     *     description="Validation error",@OA\JsonContent
     *     (ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     *     response="403",
     *     description="Not Authorized Invalid or missing Authorization header",@OA\
     *     JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     *     response=500,
     *     description="Unexpected error",@OA\JsonContent
     *     (ref="#/components/schemas/ErrorResponse")
     * ),
     * security={
     *     {"API-Key": {}}
     * }
     * )
     */

    public function gotOneAllProduct(Request $request)
    {
        try{
            $input = $request->all();

            $requiredParams = $this->requiredRequestParams('got_one_product');
            $validator = Validator::make($input, $requiredParams);
            if ($validator->fails()) 
            {
                return response()->json(['status' => "false", 'data' => "", 'messages' => array(implode(', ', $validator->errors()->all()))]);
            }

            if($input['user_id'] != Auth::user()->id)
            {
                return response()->json(['status' => "false",'data' => "", 'messages' => array('Unauthorized access')]);
            }

            $admin = Admin::first();
            $buyerProductGet = BuyerProducts::where('user_id','!=',$input['user_id'])->where('buyer_product_status',1)->where('is_purchased',0)->where('is_active',1)->orderBy('id', 'DESC');

            $dataCount = $buyerProductGet->count();

            if (empty($input['perpage'])) {
                $input['perpage'] = !empty($dataCount) ? $dataCount : 10;
                if (!empty($input['page'])){
                    $input['perpage'] = empty($dataCount) ? $dataCount : 10;
                }
            }
            $buyerProductGet = $buyerProductGet->paginate($input['perpage']);
            $buyerProductGet->appends(request()->query())->links();
            $buyerProductGet = $buyerProductGet->toArray();

            $product_array = array();
            foreach($buyerProductGet['data'] as $data)
            {
                $product_data['buyer_product_id'] = $data['id'];
                $product_data['buyer_product_name'] = $data['buyer_product_name'];
                $product_data['buyer_product_images'] = "";
                if($data['buyer_product_images'])
                {
                    $image_name = explode(',',$data['buyer_product_images']);
                    $product_data['buyer_product_images'] = asset("public/upload/buyer_thumbnail/".$image_name[0]);
                }
                array_push($product_array, $product_data);
            }

            $buyerProductGet['data'] = $product_array;

            return response()->json(['status' => "true",'data' => $buyerProductGet, 'messages' => array('Got one all product list found')]);

        } catch (Exception $e) {
            return $this->sendErrorResponse($e);
        } catch (RequestException $e) {
            return $this->sendErrorResponse($e);
        }
    }

    /**
     * Swagger defination got one single product
     *
     * @OA\Post(
     *     tags={"Got One Product"},
     *     path="/gotOneSingleProduct",
     *     description="got one single product",
     *     summary="got one single product",
     *     operationId="gotOneSingleProduct",
     * @OA\Parameter(
     *     name="Content-Language",
     *     in="header",
     *     description="Content-Language",
     *     required=false,@OA\Schema(type="string")
     *     ),
     * @OA\RequestBody(
     *     required=true,
     * @OA\MediaType(
     *     mediaType="multipart/form-data",
     * @OA\JsonContent(
     * @OA\Property(
     *     property="user_id",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="buyer_product_id",
     *     type="string"
     *     ),
     *    )
     *   ),
     *  ),
     * @OA\Response(
     *     response=200,
     *     description="User response",@OA\JsonContent
     *     (ref="#/components/schemas/SuccessResponse")
     * ),
     * @OA\Response(
     *     response="400",
     *     description="Validation error",@OA\JsonContent
     *     (ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     *     response="403",
     *     description="Not Authorized Invalid or missing Authorization header",@OA\
     *     JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     *     response=500,
     *     description="Unexpected error",@OA\JsonContent
     *     (ref="#/components/schemas/ErrorResponse")
     * ),
     * security={
     *     {"API-Key": {}}
     * }
     * )
     */

    public function gotOneSingleProduct(Request $request)
    {
        try{
            $input = $request->all();

            $requiredParams = $this->requiredRequestParams('common_validation');
            $validator = Validator::make($input, $requiredParams);
            if ($validator->fails()) 
            {
                return response()->json(['status' => "false", 'data' => "", 'messages' => array(implode(', ', $validator->errors()->all()))]);
            }

            if($input['user_id'] != Auth::user()->id)
            {
                return response()->json(['status' => "false",'data' => "", 'messages' => array('Unauthorized access')]);
            }

            $gotOnebuyerProductGet = BuyerProducts::where('user_id','!=',$input['user_id'])->where('id',$input['buyer_product_id'])->where('buyer_product_status',1)->where('is_purchased',0)->where('is_active',1)->first();

            if($gotOnebuyerProductGet)
            {
                $image_array_store = array();
                foreach(explode(',',$gotOnebuyerProductGet->buyer_product_images) as $image_name)
                {
                    array_push($image_array_store, asset("public/upload/buyer_product_images/".$image_name));
                }
    
                $data['buyer_product_id'] = $gotOnebuyerProductGet->id;
                $data['buyer_product_name'] = $gotOnebuyerProductGet->buyer_product_name;
                $data['buyer_product_description'] = $gotOnebuyerProductGet->buyer_product_description;
                $data['buyer_product_status'] = $gotOnebuyerProductGet->buyer_product_status;
                $data['buyer_product_images'] = $image_array_store;
    
                return response()->json(['status' => "true",'data' => $data, 'messages' => array('Got one product list found')]);
            }
            else
            {
                return response()->json(['status' => "false",'data' => "", 'messages' => array('Something went wrong!')]);
            }

        } catch (Exception $e) {
            return $this->sendErrorResponse($e);
        } catch (RequestException $e) {
            return $this->sendErrorResponse($e);
        }
    }

    /**
     * Swagger defination Approved Seller All Product List By Buyer Product Id
     *
     * @OA\Post(
     *     tags={"Approved Seller Product List"},
     *     path="/approvedSellerAllProductList",
     *     description="
     *  Approved Seller All Product List By Buyer Product Id",
     *     summary="Approved Seller All Product List By Buyer Product Id",
     *     operationId="approvedSellerAllProductList",
     * @OA\Parameter(
     *     name="Content-Language",
     *     in="header",
     *     description="Content-Language",
     *     required=false,@OA\Schema(type="string")
     *     ),
     * @OA\RequestBody(
     *     required=true,
     * @OA\MediaType(
     *     mediaType="multipart/form-data",
     * @OA\JsonContent(
     * @OA\Property(
     *     property="user_id",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="buyer_product_id",
     *     type="string"
     *     ),
     *    )
     *   ),
     *  ),
     * @OA\Response(
     *     response=200,
     *     description="User response",@OA\JsonContent
     *     (ref="#/components/schemas/SuccessResponse")
     * ),
     * @OA\Response(
     *     response="400",
     *     description="Validation error",@OA\JsonContent
     *     (ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     *     response="403",
     *     description="Not Authorized Invalid or missing Authorization header",@OA\
     *     JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     *     response=500,
     *     description="Unexpected error",@OA\JsonContent
     *     (ref="#/components/schemas/ErrorResponse")
     * ),
     * security={
     *     {"API-Key": {}}
     * }
     * )
     */

    public function approvedSellerAllProductList(Request $request)
    {
        try{
            $input = $request->all();

            $requiredParams = $this->requiredRequestParams('common_validation');
            $validator = Validator::make($input, $requiredParams);
            if ($validator->fails()) 
            {
                return response()->json(['status' => "false", 'messages' => array(implode(', ', $validator->errors()->all()))]);
            }

            if($input['user_id'] != Auth::user()->id)
            {
                return $this->sendBadRequest('Unauthorized access');
            }
            $admin = Admin::first();
            $sellerProduct = SellerProducts::with('wishlist')->where('is_purchased',0)->where('is_active',1)->where('user_id','!=',$input['user_id'])->where('buyer_product_id',$input['buyer_product_id'])->where('seller_product_status',1)->where('created_at', '>=', Carbon::now()->subDays($admin->seller_days));
            
            $sellerProductGet = $sellerProduct->get();
            if(!$sellerProductGet->isEmpty())
            {
                $seller_approve_data = array();
                foreach($sellerProductGet as $sellerData)
                {
                    $seller_image = "";
                    if($image_name = explode(',',$sellerData->seller_product_images))
                    {
                        $seller_image = asset("public/upload/seller_thumbnail/".$image_name[0]);
                    }
        
                    $data['seller_product_id'] = $sellerData->id;
                    $data['buyer_product_id'] = $sellerData->buyer_product_id;
                    $data['seller_product_name'] = $sellerData->seller_product_name;
                    $data['seller_product_price'] = $sellerData->seller_product_price;
                    $data['seller_product_images'] = $seller_image;
                    $data['wishlist_status'] = $sellerData->wishlist ? $sellerData->wishlist->status : 0;
                    array_push($seller_approve_data, $data);
                }
                $sellerProductCount = count($seller_approve_data);
    
                return response()->json(['status' => "true",'data' => ['seller_product_count' => $sellerProductCount, 'seller_product_data' => $seller_approve_data] , 'messages' => array('Seller product list found')]);
            }
            else
            {
                return response()->json(['status' => "true", 'data' => "", 'messages' => array('Product Not Found')]);
            }

        } catch (Exception $e) {
            return $this->sendErrorResponse($e);
        } catch (RequestException $e) {
            return $this->sendErrorResponse($e);
        }
    }

    /**
     * Swagger defination Seller All Product List By Buyer Product Id
     *
     * @OA\Post(
     *     tags={"Seller Product List"},
     *     path="/sellerAllProductList",
     *     description="
     *  Seller All Product List By Buyer Product Id",
     *     summary="Seller All Product List By Buyer Product Id",
     *     operationId="sellerAllProductList",
     * @OA\Parameter(
     *     name="Content-Language",
     *     in="header",
     *     description="Content-Language",
     *     required=false,@OA\Schema(type="string")
     *     ),
     * @OA\RequestBody(
     *     required=true,
     * @OA\MediaType(
     *     mediaType="multipart/form-data",
     * @OA\JsonContent(
     * @OA\Property(
     *     property="user_id",
     *     type="string"
     *     ),
     *    )
     *   ),
     *  ),
     * @OA\Response(
     *     response=200,
     *     description="User response",@OA\JsonContent
     *     (ref="#/components/schemas/SuccessResponse")
     * ),
     * @OA\Response(
     *     response="400",
     *     description="Validation error",@OA\JsonContent
     *     (ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     *     response="403",
     *     description="Not Authorized Invalid or missing Authorization header",@OA\
     *     JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     *     response=500,
     *     description="Unexpected error",@OA\JsonContent
     *     (ref="#/components/schemas/ErrorResponse")
     * ),
     * security={
     *     {"API-Key": {}}
     * }
     * )
     */
    public function sellerAllProductList(Request $request)
    {
        try{
            $input = $request->all();

            if($input['user_id'] != Auth::user()->id)
            {
                return $this->sendBadRequest('Unauthorized access');
            }
            $admin = Admin::first();

            $sellerProduct = SellerProducts::where('is_active',1)->where('is_purchased',0)->where('user_id','!=',$input['user_id'])->where('seller_product_status',1)->where('created_at', '>=', Carbon::now()->subDays($admin->seller_days));
            
            $sellerProductGet = $sellerProduct->get();
            if(!$sellerProductGet->isEmpty())
            {
                $seller_approve_data = array();
                foreach($sellerProductGet as $sellerData)
                {
                    $seller_image = "";
                    if($image_name = explode(',',$sellerData->seller_product_images))
                    {
                        $seller_image = asset("public/upload/seller_thumbnail/".$image_name[0]);
                    }
        
                    $data['seller_product_id'] = $sellerData->id;
                    $data['buyer_product_id'] = $sellerData->buyer_product_id;
                    $data['seller_product_name'] = $sellerData->seller_product_name;
                    $data['seller_product_price'] = $sellerData->seller_product_price;
                    $data['seller_product_images'] = $seller_image;
                    array_push($seller_approve_data, $data);
                }
                $sellerProductCount = count($seller_approve_data);
    
                return response()->json(['status' => "true",'data' => ['seller_product_count' => $sellerProductCount, 'seller_product_data' => $seller_approve_data] , 'messages' => array('Seller product list found')]);
            }
            else
            {
                return response()->json(['status' => "false", 'data' => "", 'messages' => array('Product Not Found')]);
            }

        } catch (Exception $e) {
            return $this->sendErrorResponse($e);
        } catch (RequestException $e) {
            return $this->sendErrorResponse($e);
        }
    }

    /**
     * Swagger defination Seller Open Product List
     *
     * @OA\Post(
     *     tags={"Seller Product List"},
     *     path="/sellerOpenProductList",
     *     description="
     *  Seller Open Product List",
     *     summary="Seller Open Product List",
     *     operationId="sellerOpenProductList",
     * @OA\Parameter(
     *     name="Content-Language",
     *     in="header",
     *     description="Content-Language",
     *     required=false,@OA\Schema(type="string")
     *     ),
     * @OA\RequestBody(
     *     required=true,
     * @OA\MediaType(
     *     mediaType="multipart/form-data",
     * @OA\JsonContent(
     * @OA\Property(
     *     property="user_id",
     *     type="string"
     *     ),
     *    )
     *   ),
     *  ),
     * @OA\Response(
     *     response=200,
     *     description="User response",@OA\JsonContent
     *     (ref="#/components/schemas/SuccessResponse")
     * ),
     * @OA\Response(
     *     response="400",
     *     description="Validation error",@OA\JsonContent
     *     (ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     *     response="403",
     *     description="Not Authorized Invalid or missing Authorization header",@OA\
     *     JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     *     response=500,
     *     description="Unexpected error",@OA\JsonContent
     *     (ref="#/components/schemas/ErrorResponse")
     * ),
     * security={
     *     {"API-Key": {}}
     * }
     * )
     */
    public function sellerOpenProductList(Request $request)
    {
        try{
            $input = $request->all();

            if($input['user_id'] != Auth::user()->id)
            {
                return $this->sendBadRequest('Unauthorized access');
            }
            $admin = Admin::first();

            $days = $admin->seller_days;
            $double_days = $admin->seller_days + $admin->seller_days;

            $date1 = Carbon::now()->subDays($double_days)->toDateTimeString();
            $date2 = Carbon::now()->subDays($days)->toDateTimeString();

            $sellerProduct = SellerProducts::with('wishlist')->where('is_purchased',0)->where('is_active',1)->where('user_id','!=',$input['user_id'])->where('seller_product_status',1)->whereBetween('created_at',[$date1, $date2]);
            
            $sellerProductGet = $sellerProduct->get();
            if(!$sellerProductGet->isEmpty())
            {
                $seller_approve_data = array();
                foreach($sellerProductGet as $sellerData)
                {
                    $seller_image = "";
                    if($image_name = explode(',',$sellerData->seller_product_images))
                    {
                        $seller_image = asset("public/upload/seller_thumbnail/".$image_name[0]);
                    }
        
                    $data['seller_product_id'] = $sellerData->id;
                    $data['buyer_product_id'] = $sellerData->buyer_product_id;
                    $data['seller_product_name'] = $sellerData->seller_product_name;
                    $data['seller_product_price'] = $sellerData->seller_product_price;
                    $data['seller_product_images'] = $seller_image;
                    $data['wishlist_status'] = $sellerData->wishlist ? $sellerData->wishlist->status : 0;
                    array_push($seller_approve_data, $data);
                }
                $sellerProductCount = count($seller_approve_data);
    
                return response()->json(['status' => "true",'data' => ['seller_product_count' => $sellerProductCount, 'seller_product_data' => $seller_approve_data] , 'messages' => array('Seller product list found')]);
            }
            else
            {
                return response()->json(['status' => "false", 'data' => "", 'messages' => array('Product Not Found')]);
            }

        } catch (Exception $e) {
            return $this->sendErrorResponse($e);
        } catch (RequestException $e) {
            return $this->sendErrorResponse($e);
        }
    }

    /**
     * Swagger defination Approved Seller One Product List By Buyer Product Id
     *
     * @OA\Post(
     *     tags={"Approved Seller Product List"},
     *     path="/approvedSellerOneProductList",
     *     description="
     *  Approved Seller One Product List By Buyer Product Id",
     *     summary="Approved Seller One Product List By Buyer Product Id",
     *     operationId="approvedSellerOneProductList",
     * @OA\Parameter(
     *     name="Content-Language",
     *     in="header",
     *     description="Content-Language",
     *     required=false,@OA\Schema(type="string")
     *     ),
     * @OA\RequestBody(
     *     required=true,
     * @OA\MediaType(
     *     mediaType="multipart/form-data",
     * @OA\JsonContent(
     * @OA\Property(
     *     property="user_id",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="buyer_product_id",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="seller_product_id",
     *     type="string"
     *     ),
     *    )
     *   ),
     *  ),
     * @OA\Response(
     *     response=200,
     *     description="User response",@OA\JsonContent
     *     (ref="#/components/schemas/SuccessResponse")
     * ),
     * @OA\Response(
     *     response="400",
     *     description="Validation error",@OA\JsonContent
     *     (ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     *     response="403",
     *     description="Not Authorized Invalid or missing Authorization header",@OA\
     *     JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     *     response=500,
     *     description="Unexpected error",@OA\JsonContent
     *     (ref="#/components/schemas/ErrorResponse")
     * ),
     * security={
     *     {"API-Key": {}}
     * }
     * )
     */

    public function approvedSellerOneProductList(Request $request)
    {
        try{
            $input = $request->all();

            $requiredParams = $this->requiredRequestParams('seller_one_product_list');
            $validator = Validator::make($input, $requiredParams);
            if ($validator->fails()) 
            {
                return response()->json(['status' => "false", 'messages' => array(implode(', ', $validator->errors()->all()))]);
            }

            if($input['user_id'] != Auth::user()->id)
            {
                return $this->sendBadRequest('Unauthorized access');
            }

            $sellerProduct = SellerProducts::with('wishlist')->where('is_active',1)->where('user_id','!=',$input['user_id'])->where('id',$input['seller_product_id'])->where('buyer_product_id',$input['buyer_product_id'])->where('seller_product_status',1)->first();

            if($sellerProduct)
            {
                $image_array_store = array();
                foreach(explode(',',$sellerProduct->seller_product_images) as $image_name)
                {
                    array_push($image_array_store, asset("public/upload/seller_product_images/".$image_name));
                }
    
                $userDetails = User::where('id',$sellerProduct->user_id)->first();
                $data['user_id'] = $sellerProduct->user_id;
                $data['fullname'] = $userDetails->fullname;
                $data['user_image'] = $userDetails->user_image;
                $data['user_id'] = $sellerProduct->user_id;
                $data['seller_product_id'] = $sellerProduct->id;
                $data['buyer_product_id'] = $sellerProduct->buyer_product_id;
                $data['seller_product_name'] = $sellerProduct->seller_product_name;
                $data['seller_product_images'] = $image_array_store;
                $data['seller_product_description'] = $sellerProduct->seller_product_description;
                $data['seller_product_price'] = $sellerProduct->seller_product_price;
                $data['seller_product_condition'] = $sellerProduct->seller_product_condition;
                $data['seller_product_location'] = $sellerProduct->seller_product_location;
                $data['seller_product_latitude'] = $sellerProduct->seller_product_latitude ? $sellerProduct->seller_product_latitude : '';
                $data['seller_product_longitude'] = $sellerProduct->seller_product_longitude ? $sellerProduct->seller_product_longitude : '';
                $data['seller_product_shipping_charges'] = $sellerProduct->seller_product_shipping_charges;
                $data['wishlist_status'] = $sellerProduct->wishlist ? $sellerProduct->wishlist->status : 0;
                $data['is_purchased'] = $sellerProduct->is_purchased;
    
                return response()->json(['status' => "true",'data' => $data , 'messages' => array('Seller product found')]);
            }
            else
            {
                return response()->json(['status' => "false", 'data' => "", 'messages' => array('Product Not Found')]);
            }

        } catch (Exception $e) {
            return $this->sendErrorResponse($e);
        } catch (RequestException $e) {
            return $this->sendErrorResponse($e);
        }
    }

    /**
     * Swagger defination buyer Product Delete
     *
     * @OA\Post(
     *     tags={"Product Listing delete"},
     *     path="/buyerProductDelete",
     *     description="buyer Product Delete",
     *     summary="buyer Product Delete",
     *     operationId="buyerProductDelete",
     * @OA\Parameter(
     *     name="Content-Language",
     *     in="header",
     *     description="Content-Language",
     *     required=false,@OA\Schema(type="string")
     *     ),
     * @OA\RequestBody(
     *     required=true,
     * @OA\MediaType(
     *     mediaType="multipart/form-data",
     * @OA\JsonContent(
     * @OA\Property(
     *     property="user_id",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="buyer_product_id",
     *     type="string"
     *     ),
     *    )
     *   ),
     *  ),
     * @OA\Response(
     *     response=200,
     *     description="User response",@OA\JsonContent
     *     (ref="#/components/schemas/SuccessResponse")
     * ),
     * @OA\Response(
     *     response="400",
     *     description="Validation error",@OA\JsonContent
     *     (ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     *     response="403",
     *     description="Not Authorized Invalid or missing Authorization header",@OA\
     *     JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     *     response=500,
     *     description="Unexpected error",@OA\JsonContent
     *     (ref="#/components/schemas/ErrorResponse")
     * ),
     * security={
     *     {"API-Key": {}}
     * }
     * )
     */
    public function buyerProductDelete(Request $request)
    {
        try{
            $input = $request->all();

            if($input['user_id'] != Auth::user()->id)
            {
                return $this->sendBadRequest('Unauthorized access');
            }

            $buyerProduct = BuyerProducts::where('id',$input['buyer_product_id'])->where('user_id',$input['user_id'])->where('is_purchased',0)->where('is_active',1)->first();
            
            if($buyerProduct)
            {
                $buyerProduct->is_active = 0;
                $buyerProduct->save();
                return response()->json(['status' => "true",'data' => "" , 'messages' => array('Product successfully delete')]);
            }
            else
            {
                return response()->json(['status' => "false", 'data' => "", 'messages' => array('Product Not Found')]);
            }

        } catch (Exception $e) {
            return $this->sendErrorResponse($e);
        } catch (RequestException $e) {
            return $this->sendErrorResponse($e);
        }
    }
    
    /**
     * Swagger defination Seller Product Delete
     *
     * @OA\Post(
     *     tags={"Product Listing delete"},
     *     path="/sellerProductDelete",
     *     description="Seller Product Delete",
     *     summary="Seller Product Delete",
     *     operationId="sellerProductDelete",
     * @OA\Parameter(
     *     name="Content-Language",
     *     in="header",
     *     description="Content-Language",
     *     required=false,@OA\Schema(type="string")
     *     ),
     * @OA\RequestBody(
     *     required=true,
     * @OA\MediaType(
     *     mediaType="multipart/form-data",
     * @OA\JsonContent(
     * @OA\Property(
     *     property="user_id",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="seller_product_id",
     *     type="string"
     *     ),
     *    )
     *   ),
     *  ),
     * @OA\Response(
     *     response=200,
     *     description="User response",@OA\JsonContent
     *     (ref="#/components/schemas/SuccessResponse")
     * ),
     * @OA\Response(
     *     response="400",
     *     description="Validation error",@OA\JsonContent
     *     (ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     *     response="403",
     *     description="Not Authorized Invalid or missing Authorization header",@OA\
     *     JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     *     response=500,
     *     description="Unexpected error",@OA\JsonContent
     *     (ref="#/components/schemas/ErrorResponse")
     * ),
     * security={
     *     {"API-Key": {}}
     * }
     * )
     */
    public function sellerProductDelete(Request $request)
    {
        try{
            $input = $request->all();

            if($input['user_id'] != Auth::user()->id)
            {
                return $this->sendBadRequest('Unauthorized access');
            }

            $sellerProduct = SellerProducts::where('id',$input['seller_product_id'])->where('user_id',$input['user_id'])->where('is_active',1)->where('is_purchased',0)->first();
            
            if($sellerProduct)
            {
                $sellerProduct->is_active = 0;
                $sellerProduct->save();
                return response()->json(['status' => "true",'data' => "" , 'messages' => array('Product successfully delete')]);
            }
            else
            {
                return response()->json(['status' => "false", 'data' => "", 'messages' => array('Product Not Found')]);
            }

        } catch (Exception $e) {
            return $this->sendErrorResponse($e);
        } catch (RequestException $e) {
            return $this->sendErrorResponse($e);
        }
    }

    /**
     * Swagger defination Add remove product in wishlist
     *
     * @OA\Post(
     *     tags={"Wishlist"},
     *     path="/wishlistAddRemoveSellerProduct",
     *     description="
     *  Add remove product in wishlist",
     *     summary="Add remove product in wishlist",
     *     operationId="wishlistAddRemoveSellerProduct",
     * @OA\Parameter(
     *     name="Content-Language",
     *     in="header",
     *     description="Content-Language",
     *     required=false,@OA\Schema(type="string")
     *     ),
     * @OA\RequestBody(
     *     required=true,
     * @OA\MediaType(
     *     mediaType="multipart/form-data",
     * @OA\JsonContent(
     * @OA\Property(
     *     property="user_id",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="buyer_product_id",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="seller_product_id",
     *     type="string"
     *     ),
     *    )
     *   ),
     *  ),
     * @OA\Response(
     *     response=200,
     *     description="User response",@OA\JsonContent
     *     (ref="#/components/schemas/SuccessResponse")
     * ),
     * @OA\Response(
     *     response="400",
     *     description="Validation error",@OA\JsonContent
     *     (ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     *     response="403",
     *     description="Not Authorized Invalid or missing Authorization header",@OA\
     *     JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     *     response=500,
     *     description="Unexpected error",@OA\JsonContent
     *     (ref="#/components/schemas/ErrorResponse")
     * ),
     * security={
     *     {"API-Key": {}}
     * }
     * )
     */
    public function wishlistAddRemoveSellerProduct(Request $request)
    {
        try{
            $input = $request->all();

            if($input['user_id'] != Auth::user()->id)
            {
                return $this->sendBadRequest('Unauthorized access');
            }

            // $buyerProduct = BuyerProducts::where('id',$input['buyer_product_id'])->where('is_active',1)->first();
            // if($buyerProduct)
            // {
                $sellerProduct = SellerProducts::where('id',$input['seller_product_id'])->where('buyer_product_id',$input['buyer_product_id'])->where('is_purchased',0)->where('is_active',1)->first();
                if($sellerProduct)
                {
                    $wishlist = Wishlist::where('seller_product_id',$input['seller_product_id'])->where('buyer_product_id',$input['buyer_product_id'])->where('user_id',$input['user_id'])->first();
                    if($wishlist)
                    {
                        $wishlist->status = $wishlist->status ? 0 : 1;
                        $wishlist->save();
                        $message = $wishlist->status ? 'added' : 'remove';
                    }
                    else
                    {
                        $data['user_id'] = $input['user_id'];
                        $data['buyer_product_id'] = $input['buyer_product_id'];
                        $data['seller_product_id'] = $input['seller_product_id'];
                        $wishlist = Wishlist::create($data);
                        $message = 'added';
                    }
                    if($wishlist)
                    {
                        return response()->json(['status' => "true",'data' => $wishlist , 'messages' => array('Product '.$message.' to your wishlist')]);
                    }
                    else
                    {
                        return response()->json(['status' => "true",'data' => "", 'messages' => array('Something went wrong!')]);
                    }
                }
                else
                {
                    return response()->json(['status' => "true", 'data' => "", 'messages' => array('Product Not Found')]);
                }
            // }

        } catch (Exception $e) {
            return $this->sendErrorResponse($e);
        } catch (RequestException $e) {
            return $this->sendErrorResponse($e);
        }
    }

    /**
     * Swagger defination Get product in wishlist
     *
     * @OA\Post(
     *     tags={"Wishlist"},
     *     path="/getWishlistProduct",
     *     description="
     *  Get product in wishlist",
     *     summary="Get product in wishlist",
     *     operationId="getWishlistProduct",
     * @OA\Parameter(
     *     name="Content-Language",
     *     in="header",
     *     description="Content-Language",
     *     required=false,@OA\Schema(type="string")
     *     ),
     * @OA\RequestBody(
     *     required=true,
     * @OA\MediaType(
     *     mediaType="multipart/form-data",
     * @OA\JsonContent(
     * @OA\Property(
     *     property="user_id",
     *     type="string"
     *     ),
     *    )
     *   ),
     *  ),
     * @OA\Response(
     *     response=200,
     *     description="User response",@OA\JsonContent
     *     (ref="#/components/schemas/SuccessResponse")
     * ),
     * @OA\Response(
     *     response="400",
     *     description="Validation error",@OA\JsonContent
     *     (ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     *     response="403",
     *     description="Not Authorized Invalid or missing Authorization header",@OA\
     *     JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     *     response=500,
     *     description="Unexpected error",@OA\JsonContent
     *     (ref="#/components/schemas/ErrorResponse")
     * ),
     * security={
     *     {"API-Key": {}}
     * }
     * )
     */
    public function getWishlistProduct(Request $request)
    {
        try{
            $input = $request->all();

            if($input['user_id'] != Auth::user()->id)
            {
                return $this->sendBadRequest('Unauthorized access');
            }

            $wishlist = Wishlist::where('user_id',$input['user_id'])->where('status',1)->pluck('seller_product_id')->toArray();
            if($wishlist)
            {
                $sellerProductGet = SellerProducts::with('wishlist')->where('is_purchased',0)->whereIn('id',$wishlist)->where('is_active',1)->orderBy('id','desc')->get();
                if(!$sellerProductGet->isEmpty())
                {
                    $seller_approve_data = array();
                    foreach($sellerProductGet as $sellerData)
                    {
                        $seller_image = "";
                        if($image_name = explode(',',$sellerData->seller_product_images))
                        {
                            $seller_image = asset("public/upload/seller_thumbnail/".$image_name[0]);
                        }
            
                        $data['seller_product_id'] = $sellerData->id;
                        $data['buyer_product_id'] = $sellerData->buyer_product_id;
                        $data['seller_product_name'] = $sellerData->seller_product_name;
                        $data['seller_product_price'] = $sellerData->seller_product_price;
                        $data['seller_product_images'] = $seller_image;
                        $data['wishlist_status'] = $sellerData->wishlist ? $sellerData->wishlist->status : 0;
                        array_push($seller_approve_data, $data);
                    }
                    $sellerProductCount = count($seller_approve_data);
        
                    return response()->json(['status' => "true",'data' => ['wishlist_count' => $sellerProductCount, 'wishlist_data' => $seller_approve_data] , 'messages' => array('Your wishlist data found')]);
                }
                else
                {
                    return response()->json(['status' => "true", 'data' => ['wishlist_count' => 0, 'wishlist_data' => array()], 'messages' => array('Your wishlist is empty!')]);
                }
                
            }
            else
            {
                return response()->json(['status' => "true", 'data' => ['wishlist_count' => 0, 'wishlist_data' => array()], 'messages' => array('Your wishlist is empty!')]);
            }

        } catch (Exception $e) {
            return $this->sendErrorResponse($e);
        } catch (RequestException $e) {
            return $this->sendErrorResponse($e);
        }
    }

    /**
     * Swagger defination payment
     *
     * @OA\Post(
     *     tags={"Payment"},
     *     path="/payment",
     *     description="
     *  payment",
     *     summary="payment",
     *     operationId="payment",
     * @OA\Parameter(
     *     name="Content-Language",
     *     in="header",
     *     description="Content-Language",
     *     required=false,@OA\Schema(type="string")
     *     ),
     * @OA\RequestBody(
     *     required=true,
     * @OA\MediaType(
     *     mediaType="multipart/form-data",
     * @OA\JsonContent(
     * @OA\Property(
     *     property="user_id",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="buyer_product_id",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="seller_product_id",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="amount",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="payment_method_types",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="transaction_id",
     *     type="string"
     *     ),
     *    )
     *   ),
     *  ),
     * @OA\Response(
     *     response=200,
     *     description="User response",@OA\JsonContent
     *     (ref="#/components/schemas/SuccessResponse")
     * ),
     * @OA\Response(
     *     response="400",
     *     description="Validation error",@OA\JsonContent
     *     (ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     *     response="403",
     *     description="Not Authorized Invalid or missing Authorization header",@OA\
     *     JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     *     response=500,
     *     description="Unexpected error",@OA\JsonContent
     *     (ref="#/components/schemas/ErrorResponse")
     * ),
     * security={
     *     {"API-Key": {}}
     * }
     * )
     */
    public function payment(Request $request)
    {
        try{
            $input = $request->all();

            if($input['user_id'] != Auth::user()->id)
            {
                return $this->sendBadRequest('Unauthorized access');
            }

            $productCheck = Payment::where('seller_product_id',$input['seller_product_id'])->where('buyer_product_id',$input['buyer_product_id'])->first();
            if($productCheck)
            {
                return response()->json(['status' => "true", 'data' => "", 'messages' => array('Product is already purchased')]);
            }

            $sellerProduct = SellerProducts::where('id',$input['seller_product_id'])->where('is_active',1)->where('buyer_product_id',$input['buyer_product_id'])->where('seller_product_status',1)->first();

            if($sellerProduct)
            {
                $data['user_id'] = $input['user_id'];
                $data['customer_id'] = Auth::user()->customer_id;
                $data['buyer_product_id'] = $input['buyer_product_id'];
                $data['seller_product_id'] = $input['seller_product_id'];
                $data['amount'] = $input['amount'];
                $data['payment_method_types'] = $input['payment_method_types'];
                $data['transaction_id'] = $input['transaction_id'];
                $paymentCreate = Payment::firstOrCreate($data);

                if($paymentCreate)
                {
                    $buyerStatusChanges = BuyerProducts::where('id',$input['buyer_product_id'])->where('user_id',$input['user_id'])->first();
                    if($buyerStatusChanges)
                    {
                        $buyerStatusChanges->is_purchased = 1;
                        $buyerStatusChanges->save();
                    }

                    $sellerStatusChanges = SellerProducts::where('id',$input['seller_product_id'])->where('buyer_product_id',$input['buyer_product_id'])->first();
                    if($sellerStatusChanges)
                    {
                        $sellerStatusChanges->is_purchased = 1;
                        $sellerStatusChanges->save();
                    }
                    
                    return response()->json(['status' => "true",'data' => $paymentCreate, 'messages' => array('Payment successfully saved')]);
                }
                else
                {
                    return response()->json(['status' => "true",'data' => "", 'messages' => array('Something went wrong!')]);
                }
            }
            else
            {
                return response()->json(['status' => "true", 'data' => "", 'messages' => array('Product Not Found')]);
            }

        } catch (Exception $e) {
            return $this->sendErrorResponse($e);
        } catch (RequestException $e) {
            return $this->sendErrorResponse($e);
        }
    }

    public function requiredRequestParams(string $action, $id = '')
    {
        switch ($action) {
            case 'common_validation':
                $params = [
                    'user_id' => 'required|exists:users,id',
                    'buyer_product_id' => 'required|exists:buyer_products,id',
                ];
                break;
            case 'seller_one_product_list':
                $params = [
                    'user_id' => 'required|exists:users,id',
                    'buyer_product_id' => 'required|exists:buyer_products,id',
                    'seller_product_id' => 'required|exists:seller_products,id',
                ];
                break;
            case 'got_one_product':
                $params = [
                    'user_id' => 'required|exists:users,id',
                ];
                break;
            default:
                $params = [];
                break;
        }
        return $params;
    }
}
