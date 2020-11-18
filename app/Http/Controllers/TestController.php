<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\User;
use App\Model\OrderModel;
use App\Model\SellerOrder;
use App\Model\SellerItem;
use App\Model\Cart;
use App\Mail\Test as TestEmail;
use Illuminate\Support\Facades\Mail;

class TestController extends Controller
{
    public function index()
    {
    	$_user = User::select('*',DB::raw('AES_DECRYPT(userPassword,userToken) as decrypted_pass'))->where('password','')->get();
        // dd($_user);
    	foreach($_user as $user)
    	{
    		if(!is_null($user->decrypted_pass))
    		{
                // dd($user->decrypted_pass);
    			// dd(bcrypt($user->decrypted_pass));
    			$update = new User;
    			$update->exists = true;
    			$update->userId = $user->userId;
    			$update->password = bcrypt($user->decrypted_pass);
    			$update->save();
    		}
    		
    	}
    }

    public function product()
    {
    	// dd(md5(1));
        // return order_number();
        $_cart = Cart::leftjoin('products','products.product_identifier','cart.product_identifier')
                     ->leftjoin('users','users.userToken','cart.userToken')
                     ->leftjoin('orders','orders.order_number','cart.cart_order_number')
                     ->whereNull('cart.product_id')
                     ->select('*','orders.id as cart_order_id')
                     ->get();
        foreach($_cart as $cart)
        {
            // dd($cart);
            $update                 = new Cart;
            $update->exists         = true;
            $update->cart_id        = $cart->cart_id;
            $update->product_id     = $cart->product_id;
            $update->user_id        = $cart->userId;
            $update->order_id       = $cart->cart_order_id;
            $update->save();
        }
    }

    public function email()
    {
        $order = OrderModel::where('order_number','LKL-36553773881246706459')
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
        $params['client_name']      = 'Jimar Zape';
        $params['client_address']   = 'Test Address';
        $params['client_contact']   = '097878979';
        $params['payment_method']   = $order->payment_method;
        $params['courier']          = $order->delivery_type;
        $params['client_email']     = 'jimarzape@gmail.com';
        $params['items']            = array();
        Mail::to($params['client_email'])->send(new TestEmail($params)); 
        // dd($params);
        // return view('mails.order', $params);
    }
}
