<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Traits\UtilityTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Buyer;
use Datatables;

class BuyerController extends Controller
{
    public function buyerProductIndex()
    {
        return view('admin.buyerProduct.index');
    }

    public function buyerProductList(Request $request)
    {
        try {
            
            $data = Buyer::select('users.id','users.fullname','buyers.*')
            ->leftJoin('users','buyers.user_id','users.id')
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
                ->addColumn('product_view', function($row){
     
                    $btn = '<a href="javascript:void(0)" id="view" class="view btn btn-success btn-sm" data-id="'. $row->id .'"><span class="fa fa-eye"></span></a>';
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
                ->rawColumns(['buyer_product_images','product_view'])
                ->make(true);
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function productView(Request $request)
    {
        $id = $request->id;
        $data = Buyer::find($id);
        $image_data = array();
        // $image = "";
        if($data->buyer_product_images)
        {
            foreach(explode(',',$data->buyer_product_images) as $image_name)
            {
                $image = asset("public/upload/buyer_product_images/".$image_name);
                // break;
                array_push($image_data,$image);
            }
        }
        $data['buyer_product_images'] = $image_data;
        return response()->json($data);
    }
}
