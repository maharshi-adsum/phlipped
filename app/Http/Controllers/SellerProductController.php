<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Traits\UtilityTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\BuyerProducts;
use App\Models\SellerProducts;
use Datatables;

class SellerProductController extends Controller
{
    use ResponseTrait, UtilityTrait;

    public function sellerProductIndex()
    {
        $product_all_count = SellerProducts::count();
        $pending_count = SellerProducts::where('seller_product_status',0)->count();
        $approved_count = SellerProducts::where('seller_product_status',1)->count();
        $disapproved_count = SellerProducts::where('seller_product_status',2)->count();
        return view('admin.sellerProduct.index',compact('product_all_count','pending_count','approved_count','disapproved_count'));
    }

    public function sellerProductPendingList(Request $request)
    {
        try {
            $data = SellerProducts::select('users.id','users.fullname','seller_products.*')
            ->leftJoin('users','seller_products.user_id','users.id')
            ->where('seller_product_status',$request->status)
            ->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('fullname', function($row){
                    return $row->fullname ? $row->fullname : '-';
                })
                ->addColumn('seller_product_name', function($row){
                    return $row->seller_product_name ? $row->seller_product_name : '-';
                })
                ->addColumn('seller_product_created_at', function($row){
                    return $row->created_at ? $row->created_at : '-';
                })
                ->addColumn('seller_product_images', function($row){
                    if($row->seller_product_images)
                    {
                        foreach(explode(',',$row->seller_product_images) as $image_name)
                        {
                            $image = asset("public/upload/seller_product_images/".$image_name);
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
                    
                    if (!empty($request->get('seller_product_search'))) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            if (Str::contains(Str::lower($row['fullname']), Str::lower($request->get('seller_product_search')))){
                                return true;
                            }else if (Str::contains(Str::lower($row['seller_product_name']), Str::lower($request->get('seller_product_search')))){
                                return true;
                            }else if (Str::contains(Str::lower($row['seller_product_created_at']), Str::lower($request->get('seller_product_search')))){
                                return true;
                            }else {
                                return false;
                            }
                        });
                    }
                })
                ->rawColumns(['seller_product_images','product_view','approve_and_disapprove_button'])
                ->make(true);
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function sellerProductView(Request $request)
    {
        $id = $request->id;
        $data = SellerProducts::find($id);
        $image_data = array();
        if($data->seller_product_images)
        {
            foreach(explode(',',$data->seller_product_images) as $image_name)
            {
                $image = asset("public/upload/seller_product_images/".$image_name);
                array_push($image_data,$image);
            }
        }
        $data['seller_product_images'] = $image_data;
        return response()->json($data);
    }

    public function sellerproductApproveDisapprove(Request $request)
    {
        $data = SellerProducts::find($request->id);
        if($data)
        {
            $data->seller_product_status = $request->status;
            $data->save();
        }

        $pending_count = SellerProducts::where('seller_product_status',0)->count();
        $approved_count = SellerProducts::where('seller_product_status',1)->count();
        $disapproved_count = SellerProducts::where('seller_product_status',2)->count();
        $data = ['pending_count' => $pending_count, 'approved_count' => $approved_count,'disapproved_count' => $disapproved_count];
        return response()->json($data);
    }
}
