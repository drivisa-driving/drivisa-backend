<?php

namespace Modules\Drivisa\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Entities\Discount;
use Modules\Drivisa\Entities\DiscountUser;
use Modules\Drivisa\Transformers\DiscountAppTransformer;
use Modules\Drivisa\Transformers\DiscountTransformer;
use Modules\Drivisa\Transformers\DiscountUserTransformer;

class DiscountController extends ApiBaseController
{
    public function getDiscounts()
    {
        try {
            return response()->json(['data' => DiscountTransformer::collection(Discount::orderBy('id','Desc')->get())], 200);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function storeDiscount(Request $request)
    {
        try {
           if(Discount::where('id','!=', $request->id)->where('code', $request->code)->exists()){
               return $this->errorMessage('Code is Duplicate', Response::HTTP_CONFLICT);
           }
            Discount::updateOrCreate(['id' => $request->id],
                [
                    'title' => $request->title,
                    'code' => $request->code,
                    'discount_amount' => $request->discount_amount,
                    'type' => $request->type,
                    'status' => $request->status,
                    'start_at' => Carbon::parse($request->start_at)->format('Y-m-d H:i:s'),
                    'expire_at' => Carbon::parse($request->expire_at)->format('Y-m-d H:i:s'),
                    'package_ids' => implode(',',$request->package_ids),
                ]);
            return $this->successMessage('Data Store Successfully', Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getCodeDetails(Request $request)
    {
        try {
            $code = Discount::where('code', $request->code)->first();
            if ($code) {
                $today = Carbon::today()->format('Y-m-d');
                $startAt = Carbon::parse($code->start_at)->format('Y-m-d');
                $expireAt = Carbon::parse($code->expire_at)->format('Y-m-d');
                if (Carbon::parse($expireAt)->lessThan(Carbon::today()))
                {
                    return $this->errorMessage('Code is Expired', Response::HTTP_SERVICE_UNAVAILABLE);
                }
                if ($code->status == 'disable' || !in_array($request->package_id,explode(',',$code->package_ids)) ){
                    if(!in_array($request->package_id,explode(',',$code->package_ids))){
                        return $this->errorMessage('Code is Not Valid for This Service', Response::HTTP_SERVICE_UNAVAILABLE);
                    }
                    return $this->errorMessage('Code is Not Valid', Response::HTTP_SERVICE_UNAVAILABLE);
                }
                return response()->json(['data' => new DiscountAppTransformer($code)]);
            } else {
                return $this->errorMessage('Code Not Found', Response::HTTP_NOT_FOUND);
            }
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode(), 200);
        }
    }

    public function storeUserCode(Request $request)
    {
        try {
            $id = $request->code_id;
            $user_id = $request->user_id;
            $total_discount = $request->total_discount;
            $discount_used_name = $request->discount_used_name;

            $code = Discount::where('id', $request->code_id)->first();
            $discount_amount = $code->discount_amount;
            $discount_type = $code->type;

            DiscountUser::updateOrCreate(['id' => $request->id],
                [
                    'user_id' => $user_id,
                    'discount_id' => $id,
                    'discount_amount' => $discount_amount,
                    'discount_type' => $discount_type,
                    'total_discount' => $total_discount,
                    'discount_used_name' => $discount_used_name,
                ]);
            return $this->successMessage('Data Store Successfully', Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode(), 200);
        }
    }

    public function deleteDiscount($id)
    {
        try {
            $code = Discount::find($id);
            if ($code) {
                $code->delete();
                return $this->successMessage('Data Delete Successfully', Response::HTTP_OK);
            } else {
                return $this->errorMessage('Code Not Found', Response::HTTP_NOT_FOUND);
            }
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode(), 200);
        }
    }

    public function discountUsers(Request $request)
    {
        $search = $request->search;
        try {
            if($search !='null' && $search !=''){
                return \response()->json(['data' => DiscountUserTransformer::collection(
                    DiscountUser::with('trainee.allUser')
                        ->where('discount_amount','LIKE','%'.$search.'%')
                        ->orWhere('discount_type','LIKE','%'.$search.'%')
                        ->orWhere('main_amount','LIKE','%'.$search.'%')
                        ->orWhere('total_discount','LIKE','%'.$search.'%')
                        ->orWhere('after_discount','LIKE','%'.$search.'%')
                        ->orWhere('discount_used_name','LIKE','%'.$search.'%')
                        ->orWhere('created_at','LIKE','%'.$search.'%')
                        ->orWhereHas('trainee.allUser',function ($user) use ($search){
                            $user->where('first_name','LIKE','%'.$search.'%')
                                ->orWhere('last_name','LIKE','%'.$search.'%');
                        })->paginate())]);
            }
            $ids = Discount::pluck('id');
            return response()->json(['data' => DiscountUserTransformer::collection(DiscountUser::with(['allDiscount','trainee', 'user'])->whereIn('discount_id',$ids)->latest()->paginate()),'total'=>DiscountUser::whereIn('discount_id',$ids)->count()], 200);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

}
