<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;
use App\Traits\UtilityTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\SellerProducts;
use App\Models\BuyerProducts;
use Auth;

class SellerProductController extends Controller
{
    use ResponseTrait, UtilityTrait;

    /**
     * Swagger defination seller post product
     *
     * @OA\Post(
     *     tags={"Seller Product"},
     *     path="/sellerPostProduct",
     *     description="seller post product",
     *     summary="seller post product",
     *     operationId="sellerPostProduct",
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
     *     property="seller_product_name",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="seller_product_images[]",
     *     type="file"
     *     ),
     * @OA\Property(
     *     property="seller_product_description",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="seller_product_price",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="seller_product_condition",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="seller_product_latitude",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="seller_product_longitude",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="seller_product_shipping_charges",
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

    public function sellerPostProduct(Request $request)
    {
        try{
            $input = $request->all();

            $requiredParams = $this->requiredRequestParams('seller_post_product');
            $validator = Validator::make($input, $requiredParams);
            if ($validator->fails()) 
            {
                return response()->json(['status' => "false", 'data' => "", 'messages' => array(implode(', ', $validator->errors()->all()))]);
            }

            if($input['user_id'] != Auth::user()->id)
            {
                return response()->json(['status' => "false",'data' => "", 'messages' => array('Unauthorized access')]);
            }

            $checkProduct = BuyerProducts::where('id',$input['buyer_product_id'])->where('buyer_product_status',1)->first();
            if(!$checkProduct)
            {
                return response()->json(['status' => "false",'data' => "", 'messages' => array('Something went wrong!')]);
            }

            $data['user_id'] = $input['user_id'];
            $data['buyer_product_id'] = $input['buyer_product_id'];
            $data['seller_product_name'] = $input['seller_product_name'];
            $data['seller_product_description'] = $input['seller_product_description'];
            $data['seller_product_price'] = $input['seller_product_price'];
            $data['seller_product_condition'] = $input['seller_product_condition'];
            $data['seller_product_latitude'] = $input['seller_product_latitude'];
            $data['seller_product_longitude'] = $input['seller_product_longitude'];
            $data['seller_product_shipping_charges'] = $input['seller_product_shipping_charges'];

            if($request->hasfile('seller_product_images'))
            {
                foreach($request->file('seller_product_images') as $file)
                {
                    $image_name = $file->getClientOriginalName();
                    $image_name = 'seller_product_images_' . rand(111111,999999) . '_' . time(). '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('upload/seller_product_images'), $image_name);
                    $dataImage[] = $image_name;
                }
                $data['seller_product_images'] = implode(',', $dataImage);
            }

            $sellerProductCreate = SellerProducts::firstOrCreate($data);
            if($sellerProductCreate)
            {
                return response()->json(['status' => "true",'data' => $sellerProductCreate->toArray(), 'messages' => array('Seller product successfully saved')]);
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
     * Swagger defination seller get product
     *
     * @OA\Post(
     *     tags={"Seller Product"},
     *     path="/sellerGetProduct",
     *     description="seller get product",
     *     summary="seller get product",
     *     operationId="sellerGetProduct",
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

    public function sellerGetProduct(Request $request)
    {
        try{
            $input = $request->all();

            $requiredParams = $this->requiredRequestParams('seller_get_product');
            $validator = Validator::make($input, $requiredParams);
            if ($validator->fails()) 
            {
                return response()->json(['status' => "false", 'data' => "", 'messages' => array(implode(', ', $validator->errors()->all()))]);
            }

            if($input['user_id'] != Auth::user()->id)
            {
                return response()->json(['status' => "false",'data' => "", 'messages' => array('Unauthorized access')]);
            }

            $sellerProductGet = SellerProducts::where('user_id',$input['user_id'])->orderBy('id', 'DESC')->get();
            if(!$sellerProductGet->isEmpty())
            {
                $product_array = array();
                foreach($sellerProductGet as $data)
                {
                    $product_data['user_id'] = $data['user_id'];
                    $product_data['buyer_product_id'] = $data['buyer_product_id'];
                    $product_data['seller_product_name'] = $data['seller_product_name'];
                    $product_data['seller_product_description'] = $data['seller_product_description'];
                    $product_data['seller_product_price'] = $data['seller_product_price'];
                    $product_data['seller_product_condition'] = $data['seller_product_condition'];
                    $product_data['seller_product_latitude'] = $data['seller_product_latitude'];
                    $product_data['seller_product_longitude'] = $data['seller_product_longitude'];
                    $product_data['seller_product_shipping_charges'] = $data['seller_product_shipping_charges'];
                    $image_array_store = array();
                    foreach(explode(',',$data->seller_product_images) as $image_name)
                    {
                        array_push($image_array_store, asset("public/upload/seller_product_images/".$image_name));
                    }
                    $product_data['seller_product_images'] = $image_array_store;
                    array_push($product_array, $product_data);
                }
                return response()->json(['status' => "true",'data' => $product_array, 'messages' => array('Seller product list found')]);
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
    
    public function requiredRequestParams(string $action, $id = '')
    {
        switch ($action) {
            case 'seller_post_product':
                $params = [
                    'user_id' => 'required|exists:users,id',
                    'buyer_product_id' => 'required|exists:buyer_products,id',
                    'seller_product_name' => 'required',
                    'seller_product_images' => 'required',
                    'seller_product_description' => 'required',
                    'seller_product_price' => 'required',
                    'seller_product_condition' => 'required',
                    'seller_product_latitude' => 'required',
                    'seller_product_longitude' => 'required',
                    'seller_product_shipping_charges' => 'required',
                ];
                break;
            case 'seller_get_product':
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
