<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;
use App\Traits\UtilityTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\BuyerProducts;
use Auth;

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
                return response()->json(['status' => "false", 'messages' => array(implode(', ', $validator->errors()->all()))]);
            }

            if($input['user_id'] != Auth::user()->id)
            {
                return $this->sendBadRequest('Unauthorized access');
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
                $product_data['buyer_product_description'] = $data['buyer_product_description'];
                $product_data['buyer_product_status'] = $data['buyer_product_status'];
                $image_array_store = array();
                foreach(explode(',',$data['buyer_product_images']) as $image_name)
                {
                    array_push($image_array_store, asset("public/upload/buyer_product_images/".$image_name));
                }
                $product_data['buyer_product_images'] = $image_array_store;
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
                return response()->json(['status' => "false", 'messages' => array(implode(', ', $validator->errors()->all()))]);
            }

            if($input['user_id'] != Auth::user()->id)
            {
                return $this->sendBadRequest('Unauthorized access');
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
                return $this->sendBadRequest('Something went wrong!');
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
