<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\OrderModel;
use App\Model\ProductModel;
use App\Model\CancelOrder;
use App\Model\Cart;
use App\Model\SellerOrder;
use App\Model\SellerItem;
use App\Model\ProdStockLogs;
use App\Model\StockModel;
use App\Model\Seller;
use App\Model\JtWeight;
use App\Model\JtDelivery;
use App\User;

class OrderController extends Controller
{
    public function order_no_gen()
    {
    	return order_number();
    }

    public function cancel_order_gen_no()
    {
    	return cancel_number();
    }

    public function status(Request $request)
    {
    	$order = OrderModel::where('order_number', $request->order_number)->first();
    	if(is_null($order))
    	{
    		return 'ERROR'; //should have a better return error, update it if the app is ready for imporvements
    	}
    	$data[]['delivery_status'] = $order->delivery_status;
    	return response()->json($data);
    }

    public function cancel_order(Request $request)
    {
    	$response['data']['code'] 			= 'error';
    	$response['data']['cancel_number'] 	= null;
		try
		{
			$order = OrderModel::where('order_number', $request->order_number)->first();
			if(!is_null($order))
			{
				$cn_number 					= cancel_number();
				$can 						= new CancelOrder;
				$can->order_id 				= $order->id;
				$can->user_token 			= $request->user_token;
				$can->order_number 			= $request->order_number;
				$can->cancellation_number 	= $cn_number;
				$can->cancellation_date 	= date_now();
				$can->save();

				$update_order 					= new OrderModel;
				$update_order->exists 			= true;
				$update_order->id 				= $order->id;
				$update_order->delivery_status 	= 5;
				$update_order->save();

				$cart['delivery_status'] = 5;
				Cart::where('cart_order_number', $request->order_number)->update($cart);

				$seller['seller_delivery_status'] = 5;
				SellerOrder::where('order_id', $order->id)->update($seller);

				$response['data']['code'] 			= 'success';
	    		$response['data']['cancel_number'] 	= $cn_number;
    		}

		}
		catch(\Exception $e)
		{
			// return "ERROR";
			// return $e->getMessage();
		}

		return response()->json($response);
		
    }

    public function cancel_list(Request $request)
    {
    	$cancel = CancelOrder::leftjoin('orders','orders.id','cancellation.order_id')
    						 ->leftjoin('cart','cart.order_id','cancellation.order_id')
    						 ->leftjoin('payment_methods','payment_methods.id','orders.order_payment_type')
    						 ->leftjoin('delivery_status','delivery_status.id','orders.delivery_status')
    						 ->leftjoin('products','products.product_id','cart.product_id')
    						 ->leftjoin('delivery_types','delivery_types.id','orders.order_delivery_type')
    						 ->where('cancellation.user_token', $request->user_token)
    						 ->selectRaw('cancellation.*,cart.*, orders.*, products.*, delivery_types.delivery_type, delivery_status.status_name, payment_methods.payment_method as pay_method')
    						 ->orderBy('cancellation.cancellation_date')
    						 ->get();

    	return response()->json($cancel);
    }

    public function checkout(Request $request)
    {
        try
        {
            $user = User::where('userToken', $request->user_token)->first();
            if(!is_null($user))
            {
                $lokalshare                     = 5;
                $share_amount                   = ($lokalshare / 100) * $request->subtotal;
                $order                          = new OrderModel;
                $order->order_number            = $request->order_number;
                $order->order_amount_due        = $request->amount_due;
                $order->order_delivery_type     = $request->delivery_id;
                $order->order_delivery_fee      = $request->delivery_fee;
                $order->order_date              = date('Y-m-d H:i:s');
                $order->order_payment_type      = 1;
                $order->order_subtotal          = $request->subtotal;
                $order->user_token              = $request->user_token;
                $order->lokal_com               = $lokalshare; 
                $order->lokal_com_amount        = $share_amount;
                $order->order_payment_source    = $request->chosen_payment == 2 ? $request->paymongo_source : '';
                $order->card_payment_method     = $request->chosen_payment == 3 ? $request->paymongo_payment_method : '';
                $order->card_payment_intent     = $request->chosen_payment == 3 ? $request->paymongo_payment_intent : '';
                $order->card_payment_client_key = $request->chosen_payment == 3 ? $request->paymongo_client_key : '';
                $order->save();

                $order_id   = $order->id;

                $_cart         = json_decode($request->product_items);
                foreach ($_cart as $key => $data) 
                {
                    $cart               = new Cart;
                    $cart->exists       = true;
                    $cart->cart_id      = $data['cart_id'];
                    $cart->cart_paid    = 'true';
                    $cart->remove       = 'true';
                    $cart->order_id     = $order_id;
                    $cart->save();
                }
                $group_arr = array();
                $_products = Cart::leftjoin('products','products.product_id','cart.product_id')
                                 ->leftjoin('stocks', function($join){
                                    $join->where('stocks.product_id','cart.product_id');
                                    $join->where('stocks.stocks_size', 'cart.size');
                                 })
                                 ->where('order_id', $order_id)
                                 ->selectRaw('products.*, products.product_id as prods_id ,cart.*, stocks.id as stock_id, stocks.stocks_quantity, stocks.stocks_size, stocks.stocks_weight, stocks.stocks_price')
                                 ->get();
                foreach($_products as $products)
                {
                    $group_arr[$products->seller_id][] = $products;
                }

                $total_payment      = 0;
                $total_delivery_fee = 0;

                foreach ($group_arr as $key => $sell_items) {

                    $seller_id      = $key;
                    $seller_number  = seller_number();
                    $net            = 0;
                    $discount       = 0;
                    $share          = 0;
                    $share_rate     = 5;
                    $seller_total   = 0;
                    $subtotal       = 0;
                    $total_weight   = 0;
                    $delivery_fee   = 50;

                    $seller_data    = Seller::data($seller_id)->first();

                    $seller                         = new SellerOrder;
                    $seller->order_id               = $order_id;
                    $seller->seller_id              = $key;
                    $seller->order_number           = $request->order_number;
                    $seller->seller_order_number    = $seller_number;
                    $seller->seller_sub_total       = $subtotal;
                    $seller->seller_delivery_fee    = $delivery_fee;
                    $seller->seller_total           = $seller_total;
                    $seller->seller_share_rate      = $share_rate;
                    $seller->seller_share           = $share;
                    $seller->seller_discount        = $discount;
                    $seller->seller_net             = $net;
                    $seller->seller_delivery_status = 1;
                    $seller->seller_remarks         = '';
                    $seller->save();

                    $seller_order_id                = $seller->seller_order_id;

                    foreach($sell_items as $items)
                    {
                        $sold_items                     = new SellerItem;
                        $sold_items->seller_order_id    = $seller_order_id;
                        $sold_items->cart_id            = $items->cart_id;
                        $sold_items->product_id         = $items->prods_id;
                        $sold_items->stock_id           = $items->stock_id;
                        $sold_items->order_qty          = $items->quantity;
                        $sold_items->size               = $items->stocks_size;
                        $sold_items->weight             = $items->stocks_weight;
                        $sold_items->selling_price      = $items->stocks_price;
                        $sold_items->selling_discount   = 0;
                        $sold_items->sold_price         = $items->stocks_price;
                        $sold_items->save();

                        $subtotal       += ($items->quantity * $items->stocks_price);
                        $total_weight   += $items->stocks_weight;
                    }

                    $is_cod         = $request->chosen_payment == 1 ? true : false;
                    $delivery_gram  = $total_weight / 1000;

                    if($request->delivery_id == 1)
                    {
                        $delivery_fee = 50;
                    }
                    else if ($request->delivery_id == 2) {
                        $delivery_fee = Self::mr_speedy($user, $seller_data, $delivery_gram);
                    }
                    else if($request->delivery_id == 3)
                    {
                        $delivery_fee = Self::jnt($user, $delivery_gram);
                    }
                    else if($request->delivery_id == 4)
                    {

                    }

                    $seller_share   = ($share_rate / 100) * $subtotal;
                    $seller_net     = $subtotal - $seller_share;
                    $seller_total   = $subtotal + $delivery_fee;

                    $selUpdate                      = new SellerOrder;
                    $selUpdate->exists              = true;
                    $selUpdate->seller_order_id     = $seller_order_id;
                    $selUpdate->seller_sub_total    = $subtotal;
                    $selUpdate->seller_share        = $seller_share;
                    $selUpdate->seller_net          = $seller_net;
                    $selUpdate->seller_total        = $seller_total;
                    $selUpdate->seller_delivery_fee = $delivery_fee;
                    $selUpdate->save();

                    $total_payment      += $seller_total;
                    $total_delivery_fee += $delivery_fee;

                }

                Self::email($user, $order_id);
            }
            else
            {
                return 'USER NOT FOUND';
            }
            
        }
        catch(\Exception $e)
        {
            return 'ERROR';
        }

    }

    public function stock_logs($items, $seller_id)
    {
        $logs               = new ProdStockLogs;
        $logs->product_id   = $items->prods_id;
        $logs->stock_id     = $items->stock_id;
        $logs->seller_id    = $seller_id;
        $logs->stock_qty    = (0 - $items->quantity);
        $logs->stock_price  = $items->stocks_price;
        $logs->stock_weight = $items->stocks_weight;
        $logs->save();

        $stocks = StockModel::where('id', $items->stock_id)->first();
        if(!is_null($stocks))
        {
            $update                     = new StockModel;
            $update->exists             = true;
            $update->id                 = $stocks->id;
            $update->stocks_quantity    = ($stocks->stocks_quantity - $items->quantity);
            $update->save();
        }
    }  

    public function mr_speedy($seller, $user, $weight = 0)
    {
        $delivery_fee = 50;
        $shipping = [
              'matter' => 'TShirts',
              'total_weight_kg' => $weight,
              'points' => [
                  [
                      'address' => $seller->street_address, //seller
                      'contact_person' => [
                          'phone' => $seller->contact_num,
                          'name' => strtoupper($seller->name)
                      ],
                  ], 
                  [
                      'address' => $user->mapAddress, //customer
                      'contact_person' => [
                          'phone' => $user->userMobile,
                          'name' => strtoupper($user->userFullName)
                      ]
                  ],

              ],
          ];
        $json = json_encode($shipping, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                  
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://robot.mrspeedy.ph/api/business/1.1/calculate-order');
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['X-DV-Auth-Token: 4D2C728310323C2B6F7FF5972247079E15D6C10E']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
        $result = curl_exec($curl); 
        if (!$result) { 
          throw new \Exception(curl_error($curl), curl_errno($curl)); 
        } 
        else
        {
            $mrspeedy = json_decode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            // var_dump($mrspeedy);
            if($mrspeedy['is_successful'])
            {
                $delivery_fee = $mrspeedy['order']['payment_amount'];
            }
            else
            {
                dd($mrspeedy);
            }
        }

        return $delivery_fee;
    }   

    public function jnt($user, $weight)
    {
        $delivery_fee = 50;
        $jnt_loc = JtDelivery::where('province', $user->userProvince)->first();
        $jnt_weight = JtWeight::where('weight_from','<=', $weight)
                              ->where('weight_from','>=', $weight)
                              ->first()->toArray();
        $index_weight   = 'fee_luzon';
        if($jnt_loc)
        {
          $index_weight = strtolower('fee_'.$jnt_loc->state_type);
        }

        if($weight_row[$index_weight])
        {
          $delivery_fee = $weight_row[$index_weight];
        }

        return $delivery_fee;
    }

    public function email($user, $order_id)
    {
        $order = OrderModel::where('orders.id',$order_id)
                            ->leftjoin('delivery_types','delivery_types.id','orders.order_delivery_type')
                            ->leftjoin('payment_methods','payment_methods.id','orders.order_payment_type')
                            ->select('orders.*','delivery_types.delivery_type','payment_methods.payment_method')
                            ->first();
        $_seller                    = SellerOrder::where('order_id', $order->id)
                                                 ->leftjoin('sellers','sellers.id','seller_order.seller_id')
                                                 ->select('seller_order.*','sellers.name as seller_name')
                                                 ->get()->toArray();
        $order_data                 = array();
        $params['total_shipping']   = 0;
        $params['subtotal']         = 0;
        $params['total']            = 0;
        $params['total_discount']   = 0;
        foreach($_seller as $seller)
        {
            $params['total_shipping']   += $seller['seller_delivery_fee'];
            $params['subtotal']         += $seller['seller_sub_total'];
            $params['total']            += $seller['seller_total'];
            $params['total_discount']   += $seller['seller_discount'];

            $temp               = array();
            $temp['seller']     = $seller['seller_name'];
            $temp['total_qty']  = 0;
            $temp['items']      = array();
            $_items = SellerItem::leftjoin('products','products.product_id','seller_order_item.product_id')
                                ->where('seller_order_id', $seller['seller_order_id'])
                                ->get();
            // dd($_items);
            foreach($_items as $items)
            {
                $temp_items = array();
                $temp_items['product_name']     = $items->product_name;
                $temp_items['sold_price']       = $items->sold_price;
                $temp_items['size']             = $items->size;
                $temp_items['order_qty']        = $items->order_qty;
                $temp_items['product_image']    = $items->product_image;
                $temp_items['selling_price']    = $items->selling_price;
                $temp['total_qty'] += $items->order_qty;

                array_push($temp['items'], $temp_items);
            }
            array_push($order_data, $temp);
        }
        $params['order_data']       = $order_data;
        $params['order_no']         = $order->order_number;
        $params['order_date']       = date("l jS \of F Y h:i:s A", strtotime($order->created_at));
        $params['from']             = 'order@lokaldatph.com';
        $params['client_name']      = $user->userFullName;
        $params['client_address']   = $user->mapAddress;
        $params['client_contact']   = $user->userMobile;
        $params['payment_method']   = $order->payment_method;
        $params['courier']          = $order->delivery_type;
        $params['client_email']     = 'jimarzape@gmail.com';
        $params['items']            = array();
        Mail::to($params['client_email'])->send(new TestEmail($params)); 
    }


    public function order_status($status, Request $request)
    {
        try
        {
            $stat = [
                'processing' => 1,
                'receive' => 3,
                'review' => 7,
                'ship' => 2
            ];
            // dd($stat[$status]);
            $order = OrderModel::leftjoin('cart','cart.order_id','orders.id')
                            ->leftjoin('products','products.product_id','cart.product_id')
                            ->where('orders.user_token', $request->user_token)
                            ->where('cart.delivery_status',$stat[$status])
                            ->groupBy('cart.cart_id')
                            ->orderBy('orders.id')
                            ->get();
            return response()->json($order, 200, [], JSON_NUMERIC_CHECK);
        }
        catch(\Exception $e)
        {
            return 'Error while processing the request.';
        }
        
    }
}
