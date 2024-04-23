<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Jobs\PingJob;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function checkout(CheckoutRequest $request){
        try {
            DB::beginTransaction();
            $user = $request->get('user');
            $item = $request->get('item');
            $order = new Order([
                'user_id' => $user["id"],
            ] + $request->except('user','item'));
            $order->save();
            $orderDetail = [];
            foreach ($item as $each){
                $orderDetail[] = [
                    'order_id' => $order->id,
                    'product_id' => $each["product_id"],
                    'number' => $each["number"],
                    'price' => $each["price"],
                    'name_product' => $each["name_product"],
                    'image_product' => $each["image_product"],
                ];
            }
            OrderDetail::insert($orderDetail);
            PingJob::dispatch($user);
            DB::commit();
            return response()->json([
                "message" => "success"
            ], 200);
        }catch (\Exception $e){
            DB::rollBack();
            dd($e);
            return response()->json([
                "message" => $e->getMessage(),
            ],500);
        }
    }
    //
}
