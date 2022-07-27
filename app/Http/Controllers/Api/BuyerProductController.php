<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;
use App\Traits\UtilityTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\BuyerProducts;
use App\Models\Admin;
use Auth;
use ImageResize;
use Carbon\Carbon;
use App\Image;

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
            $messsages = array('buyer_product_images.*.required'=>'The buyer product images field is required.');
            $validator = Validator::make($input, $requiredParams, $messsages);
            if ($validator->fails())
            {
                return response()->json(['status' => "false", 'data' => "", 'messages' => array(implode(', ', $validator->errors()->all()))]);
            }

            if($input['user_id'] != Auth::user()->id)
            {
                return response()->json(['status' => "false", 'data' => "", 'messages' => array('Unauthorized access')]);
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
                    // $img = ImageResize::make($file->path());
                    // $img->resize(150, 100, function ($constraint) {
                    //     $constraint->aspectRatio();
                    // })->save(public_path('upload/buyer_thumbnail').'/'.$image_name);

                    $imageResize = ImageResize::make($file->path());
                    $imageResize->orientate()
                    ->fit(150, 100, function ($constraint) {
                        $constraint->upsize();
                    })
                    ->save(public_path('upload/buyer_thumbnail').'/'.$image_name);

                    $file->move(public_path('upload/buyer_product_images'), $image_name);
                    $dataImage[] = $image_name;
                    $data['buyer_product_images'] = implode(',', $dataImage);
                }
            }

            $buyerProductCreate = BuyerProducts::firstOrCreate($data);
            if($buyerProductCreate)
            {
                return response()->json(['status' => "true",'data' => $buyerProductCreate->toArray(), 'messages' => array('Buyer product successfully saved')]);
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
     * Swagger defination buyer get product
     *
     * @OA\Post(
     *     tags={"Buyer Product"},
     *     path="/buyerGetProduct",
     *     description="buyer get product",
     *     summary="buyer get product",
     *     operationId="buyerGetProduct",
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

    public function buyerGetProduct(Request $request)
    {
        try{
            $input = $request->all();

            $requiredParams = $this->requiredRequestParams('buyer_get_product');
            $validator = Validator::make($input, $requiredParams);
            if ($validator->fails())
            {
                return response()->json(['status' => "false", 'data' => "", 'messages' => array(implode(', ', $validator->errors()->all()))]);
            }

            if($input['user_id'] != Auth::user()->id)
            {
                return response()->json(['status' => "false", 'data' => "", 'messages' => array('Unauthorized access')]);
            }
            $admin = Admin::first();

            $buyerProductGet = BuyerProducts::with(['sellerProduct' =>function($q)use($admin){
                                        $q->where('seller_product_status',1)->where('created_at', '>=', Carbon::now()->subDays($admin->seller_days));
                                }])->where('user_id',$input['user_id'])->where('purchased_user_id',0)->where('is_active',1)->orderBy('id', 'DESC')->get();

            if(!$buyerProductGet->isEmpty())
            {
                $product_array = array();
                foreach($buyerProductGet as $data)
                {
                    $sellerProductPrice = array();
                    if(!$data->sellerProduct->isEmpty())
                    {
                        foreach($data->sellerProduct as $sellerProduct)
                        {
                            array_push($sellerProductPrice, $sellerProduct->seller_product_price);
                        }
                    }
                    else
                    {
                        array_push($sellerProductPrice, 0);
                    }

                    $product_data['buyer_product_id'] = $data['id'];
                    $product_data['buyer_product_name'] = $data['buyer_product_name'];
                    $product_data['buyer_product_description'] = $data['buyer_product_description'];
                    $product_data['buyer_product_status'] = $data['buyer_product_status'];
                    $product_data['highest_price'] = isset($sellerProductPrice) ? max($sellerProductPrice) : 0;
                    $product_data['lowest_price'] = isset($sellerProductPrice) ? min($sellerProductPrice) : 0;
                    $image_array_store = array();
                    if($data->buyer_product_images)
                    {
                        foreach(explode(',',$data->buyer_product_images) as $image_name)
                        {
                            array_push($image_array_store, asset("public/upload/buyer_thumbnail/".$image_name));
                        }
                    }
                    $product_data['buyer_product_images'] = $image_array_store;
                    array_push($product_array, $product_data);
                }
                return response()->json(['status' => "true", 'data' => $product_array, 'messages' => array('Buyer product list found')]);
            }
            else
            {
                return response()->json(['status' => "false", 'data' => "", 'messages' => array('Something went wrong!')]);
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
                    'user_id' => 'required|exists:users,id',
                    'buyer_product_name' => 'required',
                    'buyer_product_images.*' => 'required',
                    'buyer_product_description' => 'required',
                ];
                break;
            case 'buyer_get_product':
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
