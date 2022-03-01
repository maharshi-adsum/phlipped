<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Traits\UtilityTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\BuyerProducts;
use App\Models\User;
use Datatables;

class BuyerProductController extends Controller
{
    use ResponseTrait, UtilityTrait;

    public function buyerProductIndex()
    {
        $product_all_count = BuyerProducts::where('is_active',1)->count();
        $pending_count = BuyerProducts::where('buyer_product_status',0)->where('is_active',1)->count();
        $approved_count = BuyerProducts::where('buyer_product_status',1)->where('is_active',1)->count();
        $disapproved_count = BuyerProducts::where('buyer_product_status',2)->where('is_active',1)->count();
        return view('admin.buyerProduct.index',compact('product_all_count','pending_count','approved_count','disapproved_count'));
    }

    public function buyerProductPendingList(Request $request)
    {
        try {
            $data = BuyerProducts::select('users.id','users.fullname','buyer_products.*')
            ->leftJoin('users','buyer_products.user_id','users.id')
            ->where('buyer_products.buyer_product_status',$request->status)
            ->where('buyer_products.is_active',1)
            ->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('fullname', function($row){
                    return $row->fullname ? $row->fullname : '-';
                })
                ->addColumn('buyer_product_name', function($row){
                    return $row->buyer_product_name ? $row->buyer_product_name : '-';
                })
                ->addColumn('buyer_product_created_at', function($row){
                    return $row->created_at ? $row->created_at : '-';
                })
                ->addColumn('buyer_product_images', function($row){
                    if($row->buyer_product_images)
                    {
                        foreach(explode(',',$row->buyer_product_images) as $image_name)
                        {
                            $image = asset("public/upload/buyer_product_images/".$image_name);
                            return '<img src=" '.$image.' " style="width: 125px; height: 125px;" class="img-thumbnail"/>';
                        }
                    }
                    return '-';
                })
                ->addColumn('approve_and_disapprove_button', function($row) use($request){
                    if($request->status == 0)
                    {
                        return '<a href="javascript:void(0)" class="approve btn btn-success btn-sm" data-id="'. $row->id .'"><span class="fas fa-thumbs-up"></span></a> <a href="javascript:void(0)" class="disapprove btn btn-danger btn-sm" data-id="'. $row->id .'"><span class="fas fa-thumbs-down"></span></a>';
                    }
                    else if($request->status == 1)
                    {
                        return '<a href="javascript:void(0)" class="btn btn-success btn-sm"><span class="fas fa-thumbs-up"></span></a>';
                    }
                    else if($request->status == 2)
                    {
                        return '<a href="javascript:void(0)" class="btn btn-danger btn-sm"><span class="fas fa-thumbs-down"></span></a>';
                    }
                })
                ->addColumn('product_view', function($row){
     
                    $btn = '<a href="javascript:void(0)" class="view btn btn-success btn-sm" data-id="'. $row->id .'"><span class="fa fa-eye"></span></a>';
                    return $btn;
                })
                ->filter(function ($instance) use ($request) {
                    
                    if (!empty($request->get('buyer_product_search'))) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            if (Str::contains(Str::lower($row['fullname']), Str::lower($request->get('buyer_product_search')))){
                                return true;
                            }else if (Str::contains(Str::lower($row['buyer_product_name']), Str::lower($request->get('buyer_product_search')))){
                                return true;
                            }else if (Str::contains(Str::lower($row['buyer_product_created_at']), Str::lower($request->get('buyer_product_search')))){
                                return true;
                            }else {
                                return false;
                            }
                        });
                    }
                })
                ->rawColumns(['buyer_product_images','product_view','approve_and_disapprove_button'])
                ->make(true);
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function productView(Request $request)
    {
        $id = $request->id;
        $data = BuyerProducts::where('is_active',1)->find($id);
        $image_data = array();
        if($data->buyer_product_images)
        {
            foreach(explode(',',$data->buyer_product_images) as $image_name)
            {
                $image = asset("public/upload/buyer_thumbnail/".$image_name);
                array_push($image_data,$image);
            }
        }else {
            $image = asset("public/assets/images/no-image.png");
            array_push($image_data,$image);
        }
        $data['buyer_product_images'] = $image_data;
        return response()->json($data);
    }

    public function productApproveDisapprove(Request $request)
    {
        $data = BuyerProducts::where('id',$request->id)->where('is_active',1)->first();
        if($data)
        {
            $data->buyer_product_status = $request->status;
            $data->save();

            $image_name = explode(',',$data->buyer_product_images);
            $imagePath = $image_name ? asset("public/upload/buyer_product_images/".$image_name[0]) : '';
            
            if($request->status == 1)
            {
                $message = [
                    "title" => "Your post is approved",
                    "body" => "Your ". $data->buyer_product_name ." post approved",
                    // "image" => $imagePath,
                    "sound" => "default"
                ];
            }

            if($request->status == 2)
            {
                $message = [
                    "title" => "Your post wasn't approved",
                    "body" => "Your ". $data->buyer_product_name ." post disapproved",
                    // "image" => $imagePath,
                    "sound" => "default"
                ];
            }

            $token = User::where('id',$data->user_id)->first();
            $this->sendSingle($token->device_token, $message);
        }

        $pending_count = BuyerProducts::where('is_active',1)->where('buyer_product_status',0)->count();
        $approved_count = BuyerProducts::where('is_active',1)->where('buyer_product_status',1)->count();
        $disapproved_count = BuyerProducts::where('is_active',1)->where('buyer_product_status',2)->count();
        $data = ['pending_count' => $pending_count, 'approved_count' => $approved_count,'disapproved_count' => $disapproved_count];
        return response()->json($data);
    }
}
