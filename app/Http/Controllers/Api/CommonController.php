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

            $buyerProductGet = BuyerProducts::where('user_id','!=',$input['user_id'])->where('buyer_product_status',1)->orderBy('id', 'DESC');

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

            $gotOnebuyerProductGet = BuyerProducts::where('user_id','!=',$input['user_id'])->where('id',$input['buyer_product_id'])->where('buyer_product_status',1)->first();

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
            $sellerProduct = SellerProducts::where('user_id','!=',$input['user_id'])->where('buyer_product_id',$input['buyer_product_id'])->where('seller_product_status',1);
            
            // $sellerProductCount = $sellerProduct->count();
            $sellerProductGet = $sellerProduct->get();
            if(!$sellerProductGet->isEmpty())
            {
                $seller_approve_data = array();
                foreach($sellerProductGet as $sellerData)
                {
                    if($sellerData->created_at->addDays($admin->day)->toDateTimeString() >= Carbon::now()){
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
                }
                $sellerProductCount = count($seller_approve_data);
    
                return response()->json(['status' => "true",'data' => ['seller_product_count' => $sellerProductCount, 'seller_product_data' => $seller_approve_data] , 'messages' => array('Seller product list found')]);
            }
            else
            {
                // return $this->sendBadRequest('Product Not Found');
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
            $sellerProduct = SellerProducts::where('user_id','!=',$input['user_id'])->where('seller_product_status',1);
            
            $sellerProductGet = $sellerProduct->get();
            if(!$sellerProductGet->isEmpty())
            {
                $seller_approve_data = array();
                foreach($sellerProductGet as $sellerData)
                {
                    if($sellerData->created_at->addDays($admin->day)->toDateTimeString() < Carbon::now()){
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
                }
                $sellerProductCount = count($seller_approve_data);
    
                return response()->json(['status' => "true",'data' => ['seller_product_count' => $sellerProductCount, 'seller_product_data' => $seller_approve_data] , 'messages' => array('Seller product list found')]);
            }
            else
            {
                // return $this->sendBadRequest('Product Not Found');
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

            $sellerProduct = SellerProducts::where('user_id','!=',$input['user_id'])->where('id',$input['seller_product_id'])->where('buyer_product_id',$input['buyer_product_id'])->where('seller_product_status',1)->first();

            if($sellerProduct)
            {
                $image_array_store = array();
                foreach(explode(',',$sellerProduct->seller_product_images) as $image_name)
                {
                    array_push($image_array_store, asset("public/upload/seller_product_images/".$image_name));
                }
    
                $data['seller_product_id'] = $sellerProduct->id;
                $data['buyer_product_id'] = $sellerProduct->buyer_product_id;
                $data['seller_product_name'] = $sellerProduct->seller_product_name;
                $data['seller_product_images'] = $image_array_store;
                $data['seller_product_description'] = $sellerProduct->seller_product_description;
                $data['seller_product_price'] = $sellerProduct->seller_product_price;
                $data['seller_product_condition'] = $sellerProduct->seller_product_condition;
                $data['seller_product_location'] = $sellerProduct->seller_product_location;
                $data['seller_product_shipping_charges'] = $sellerProduct->seller_product_shipping_charges;
    
                return response()->json(['status' => "true",'data' => $data , 'messages' => array('Seller product found')]);
            }
            else
            {
                // return $this->sendBadRequest('Product Not Found');
                return response()->json(['status' => "false", 'data' => "", 'messages' => array('Product Not Found')]);
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
