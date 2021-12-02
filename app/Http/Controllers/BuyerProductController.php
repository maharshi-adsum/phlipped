<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Traits\UtilityTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\BuyerProducts;
use Datatables;

class BuyerProductController extends Controller
{
    use ResponseTrait, UtilityTrait;

    public function buyerProductIndex()
    {
        return view('admin.buyerProduct.index');
    }

    public function buyerProductList(Request $request)
    {
        try {
            
            $data = BuyerProducts::select('users.id','users.fullname','buyer_products.*')
            ->leftJoin('users','buyer_products.user_id','users.id')
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
                ->addColumn('approve_and_disapprove_button', function($row){
                    return '<a href="javascript:void(0)" class="approve btn btn-success btn-sm" data-id="'. $row->id .'"><span class="fas fa-thumbs-up"></span></a> <a href="javascript:void(0)" class="disapprove btn btn-danger btn-sm" data-id="'. $row->id .'"><span class="fas fa-thumbs-down"></span></a>';
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
        $data = BuyerProducts::find($id);
        $image_data = array();
        if($data->buyer_product_images)
        {
            foreach(explode(',',$data->buyer_product_images) as $image_name)
            {
                $image = asset("public/upload/buyer_product_images/".$image_name);
                array_push($image_data,$image);
            }
        }
        $data['buyer_product_images'] = $image_data;
        return response()->json($data);
    }

    public function productApprove(Request $request)
    {
        $data = BuyerProducts::find($request->id);
        if($data)
        {
            $data->product_status = 1;
            $data->save();
            return response()->json($data);
        }
        else
        {
            return response()->json($data);
        }
    }

    public function productDisapprove(Request $request)
    {
        $data = BuyerProducts::find($request->id);
        if($data)
        {
            $data->product_status = 2;
            $data->save();
            return response()->json($data);
        }
        else
        {
            return response()->json($data);
        }
    }
}
