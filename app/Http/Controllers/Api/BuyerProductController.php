<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;
use App\Traits\UtilityTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\BuyerProducts;
use Auth;

class BuyerProductController extends Controller
{
    use ResponseTrait, UtilityTrait;

    /**
     * Swagger defination buyer post product
     *
     * @OA\Post(
     *     tags={"Buyer Product"},
     *     path="/buyerPostProduct",
     *     description="buyer post product",
     *     summary="buyer post product",
     *     operationId="buyerPostProduct",
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
     *     property="buyer_product_name",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="buyer_product_images[]",
     *     type="file"
     *     ),
     * @OA\Property(
     *     property="buyer_product_description",
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

    public function buyerPostProduct(Request $request)
    {
        try{
            $input = $request->all();

            $requiredParams = $this->requiredRequestParams('buyer_post_product');
            $validator = Validator::make($input, $requiredParams);
            if ($validator->fails()) 
            {
                return response()->json(['status' => "false", 'messages' => array(implode(', ', $validator->errors()->all()))]);
            }

            if($input['user_id'] != Auth::user()->id)
            {
                return $this->sendBadRequest('Unauthorized access');
            }

            $data['user_id'] = $input['user_id'];
            $data['buyer_product_name'] = $input['buyer_product_name'];
            $data['buyer_product_description'] = $input['buyer_product_description'];

            if($request->hasfile('buyer_product_images'))
            {
                foreach($request->file('buyer_product_images') as $file)
                {
                    $image_name = $file->getClientOriginalName();
                    $image_name = 'buyer_product_images_' . rand(111111,999999) . '_' . time(). '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('upload/buyer_product_images'), $image_name);
                    $dataImage[] = $image_name;
                }
                $data['buyer_product_images'] = implode(',', $dataImage);
            }

            $buyerProductCreate = BuyerProducts::firstOrCreate($data);
            if($buyerProductCreate)
            {
                return response()->json(['status' => "true",'data' => $buyerProductCreate->toArray(), 'messages' => array('Buyer product successfully saved')]);
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
            case 'buyer_post_product':
                $params = [
                    'user_id' => 'required',
                    'buyer_product_name' => 'required',
                    'buyer_product_images' => 'required',
                    'buyer_product_description' => 'required',
                ];
                break;
            case 'user_profile_get':
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
