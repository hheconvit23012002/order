<?php

namespace App\Http\Controllers;

use App\Events\CheckoutEvent;
use App\Http\Requests\CheckoutRequest;
use App\Jobs\PingJob;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CheckoutController extends Controller
{
    public function checkout(CheckoutRequest $request){
        try {

            DB::beginTransaction();
            $user = $request->get('user');
            $item = $request->get('item');


            broadcast(new CheckoutEvent('check validate product'));

            $response = Http::withHeaders([
                "Accept" => "application/json",
            ])->post('http://127.0.0.1:5053/api/book/checkValidate',[
                'item' => $item
            ]);

            if($response->status() !== 200){
                throw new \Exception('validate product failed');
            }

            sleep(10);

            broadcast(new CheckoutEvent('check validate card'));


            $response = Http::withHeaders([
                "Accept" => "application/json",
            ])->get('http://127.0.0.1:5052/api/checkCard');

            if($response->status() !== 200){
                throw new \Exception('Card not found');
            }

            sleep(10);

            broadcast(new CheckoutEvent('update product'));


            $response = Http::withHeaders([
                "Accept" => "application/json",
            ])->post('http://127.0.0.1:5053/api/book/updateProduct',[
                'item' => $item
            ]);

            if($response->status() !== 200){
                throw new \Exception('update product failed');
            }

            sleep(10);

            broadcast(new CheckoutEvent('save order'));

            $order = [
                    'user_id' => $user["id"],
                ] + $request->except('user','item');
            $response = Http::withHeaders([
                "Accept" => "application/json",
            ])->post('http://127.0.0.1:5051/api/saveOrder',[
                'order' => $order,
                'item' => $item
            ]);

            if($response->status() !== 200){
                throw new \Exception('Save order failed');
            }
            $orderId = $response->json();

            sleep(10);

            broadcast(new CheckoutEvent('payment processing'));

            $response = Http::withHeaders([
                "Accept" => "application/json",
            ])->post('http://127.0.0.1:5052/api/payment',[
                'orderId' => $orderId['order_id'],
            ]);

            if($response->status() !== 200){
                throw new \Exception('Payment failed');
            }

            sleep(10);


            PingJob::dispatch($user);
            DB::commit();
            return response()->json([
                "message" => "success"
            ], 200);
        }catch (\Exception $e){
            DB::rollBack();
            return response()->json([
                "message" => $e->getMessage(),
            ],500);
        }
    }
    //
}
