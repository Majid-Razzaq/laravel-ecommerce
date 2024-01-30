<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\CustomerAddress;
use App\Models\DiscountCoupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShippingCharge;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {


        $product = Product::with('product_images')->find($request->id);
        if($product == null)
        {
            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ]);
        }
        // If product alredy exists in Cart data
        if(Cart::count() > 0)
        {
            //echo "Product already in cart";
            //Products Found in Cart
            // Check if this product already in the cart
            // Return a message that product already added in your cart
            // If product not found in the cart, then add product in the cart

            $cartContent = Cart::content();
            $productAlreadyExist = false;

            foreach ($cartContent as $item) {
                if($item->id == $product->id)
                {
                    $productAlreadyExist = true;
                }
            }

            if($productAlreadyExist == false)
            {
                Cart::add($product->id, $product->title, 1, $product->price,['productImage' => (!empty($product->product_images)) ? $product->product_images->first() : '' ]);
                $status = true;
                $message = '<strong>'.$product->title.'</strong> added in your Cart successfully.';
                Session()->flash('message',$message);
            }
            else{
                $status = false;
                $message = $product->title.' already added in Cart';
            }

        }
        else{

            Cart::add($product->id, $product->title, 1, $product->price,['productImage' => (!empty($product->product_images)) ? $product->product_images->first() : '' ]);
            $status = true;
            $message = '<strong>'.$product->title.'</strong> added in your Cart successfully.';
            Session()->flash('message',$message);
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
        ]);
    }


    // Cart function
    public function cart()
    {
        // dd(Cart::content());
        $cartContent = Cart::content();
        //dd($cartContent);
        $data['cartContent'] = $cartContent;
        return view('front.cart',$data);
    }

    // Update cart qty
    public function updateCart(Request $request)
    {
        $rowId = $request->rowId;
        $qty = $request->qty;


        $itemInfo = Cart::get($rowId);
        $product = Product::find($itemInfo->id);
        // Check Qty availible in stock
        if($product->track_qty == 'Yes')
        {
            if($qty <= $product->qty)
            {
                Cart::update($rowId, $qty);
                $message = 'Cart updated successfully';
                $status = true;
                Session()->flash('message',$message);
            }else{
                $message = "Requested quantity($qty) not available in stock.";
                $status = false;
                Session()->flash('error',$message);
            }
        }
        else{
            Cart::update($rowId, $qty);
            $message = 'Cart updated successfully';
            $status = true;
            Session()->flash('message',$message);
        }


        return response()->json([
            'status' => $status,
            'message' => $message,
        ]);
    }

    // Delete function row from Cart
    public function deleteItem(Request $request)
    {
        $itemInfo = Cart::get($request->rowId);
        if($itemInfo == null)
        {
            $errorMsg = 'Item not found in Cart';
            Session()->flash('error',$errorMsg);
            return response()->json([
                'status' => false,
                'message' => $errorMsg,
            ]);
        }

        Cart::remove($request->rowId);

        // If Item is availible remove message will be shown
        $message = 'Item remove from Cart successfully.';
        Session()->flash('message',$message);
        return response()->json([
            'status' => true,
            'message' => $message,
        ]);


    }

    public function checkout()
    {
        $discount = 0;

        // Condition based on If cart is empty redirect to cart page
        if(Cart::count() == 0)
        {
            return redirect()->route('front.cart');
        }

        // Condition based on If User is not logged and click on to (addToCart) Button then redirect to login page
        if(Auth::check() == false)
        {
            // This session save our url
            if(!session()->has('url.intended'))
            {
                Session(['url.intended' => url()->current()]);
            }

            return redirect()->route('account.login');
        }

        $customerAddress = CustomerAddress::where('user_id',Auth::user()->id)->first();

        Session()->forget('url.intended');

        // This command fetch out country data from countries table
        $countries = Country::orderBy('name','ASC')->get();


        $subTotal = Cart::subtotal(2,'.','');
           // Apply Dicount here
           if(session()->has('code')){
            $code = session()->get('code');
             if($code->type == 'percent'){
                 $discount = ($code->discount_amount/100)*$subTotal;
             }
             else{
                 $discount = $code->discount_amount;
             }
         }


        // Calculate shipping here
        if($customerAddress !== null)
        {
            $userCountry = $customerAddress->country_id;
            $shippingInfo = ShippingCharge::where('country_id',$userCountry)->first();

            // echo $shippingInfo->amount;
            $totalQty = 0;
            $totalShippingCharge = 0;
            $grandTotal = 0;

            foreach (Cart::content() as $item) {
                $totalQty += $item->qty;
            }

            if ($shippingInfo !== null) {
                $totalShippingCharge = $totalQty * $shippingInfo->amount;
            } else {
                // Handle the case where $shippingInfo is null, perhaps by setting a default value.
                $totalShippingCharge = 0; // Or any other appropriate default value.
            }
            // $totalShippingCharge = $totalQty*$shippingInfo->amount; // there is a error


            $grandTotal = ($subTotal-$discount) + $totalShippingCharge;
        }
        else{
            $grandTotal = ($subTotal-$discount);
            $totalShippingCharge = 0;
        }

        return view('front.checkout',[
            'countries' => $countries,
            'customerAddress' => $customerAddress,
            'totalShippingCharge' => $totalShippingCharge,
            'discount' => $discount,
            'grandTotal' => $grandTotal,

        ]);
    }

    // When User click on payNow this function will call
    public function processCheckout(Request $request){

        // Step -1 Apply validation
        $validator = Validator::make($request->all(),[
            'first_name' => 'required|min:5',
            'last_name' => 'required',
            'email' => 'required|email',
            'country' => 'required',
            'address' => 'required|min:30',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'mobile' => 'required',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'message' => 'Plesae fix the errors',
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

        // Step -2 save user address
        // $customerAddress = CustomerAddress::find();
        $user = Auth::user();

        CustomerAddress::updateOrCreate(
            ['user_id' => $user->id],
            [
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'country_id' => $request->country,
                'address' => $request->address,
                'apartment' => $request->appartment,
                'city' => $request->city,
                'state' => $request->state,
                'zip' => $request->zip,
            ]
            );

            // Step-3 Store data in orders table
            if($request->payment_method == 'cod')
            {

                $discountCodeId = NULL;
                $promoCode = '';
                $shipping = 0;
                $discount = 0;
                $subTotal = Cart::subtotal(2,'.','');



            // Apply Dicount here
            if(session()->has('code')){
                $code = session()->get('code');
                if($code->type == 'percent'){
                    $discount = ($code->discount_amount/100)*$subTotal;
                }
                else{
                    $discount = $code->discount_amount;
                }

                $discountCodeId = $code->id;
                $promoCode = $code->code;
            }

                // Calculate Shipping
                $shippingInfo = ShippingCharge::where('country_id',$request->country)->first();
                $totalQty = 0;

                foreach (Cart::content() as $item) {
                    $totalQty += $item->qty;
                }

                if($shippingInfo != null)
                {
                    $shipping = $totalQty*$shippingInfo->amount;
                    $grandTotal = ($subTotal-$discount) + $shipping;

                }
                else{
                    $shippingInfo = ShippingCharge::where('country_id','rest_of_world')->first();
                    $shipping = $totalQty * $shippingInfo->amount;
                    $grandTotal = ($subTotal-$discount) + $shipping;
                }



                $order = new Order;
                $order->subtotal = $subTotal;
                $order->shipping = $shipping;
                $order->grand_total = $grandTotal;
                $order->discount = $discount;
                $order->coupon_code_id = $discountCodeId;
                $order->coupon_code = $promoCode;
                $order->payment_status = 'not paid'; //  We add 'not paid' because we are in COD functionality where user dont pay directly
                $order->status = 'pending'; // like payment status we add pending here too
                $order->user_id = $user->id;
                $order->first_name = $request->first_name;
                $order->last_name = $request->last_name;
                $order->email = $request->email;
                $order->mobile= $request->mobile;
                $order->address = $request->address;
                $order->apartment = $request->appartment;
                $order->state = $request->state;
                $order->city = $request->city;
                $order->zip = $request->zip;
                $order->notes = $request->order_notes;
                $order->country_id = $request->country;

                $order->save();

                // Step-4 Store order items in order Items table

                foreach (Cart::content() as $item) {
                    $orderItem = new OrderItem;
                    $orderItem->product_id = $item->id;
                    $orderItem->order_id = $order->id;
                    $orderItem->name = $item->name;
                    $orderItem->qty = $item->qty;
                    $orderItem->price = $item->price;
                    $orderItem->total = $item->price*$item->qty;
                    $orderItem->save();

                    // update Product Stock
                    $productData = Product::find($item->id);
                    if($productData->track_qty == 'Yes')
                    {
                        $currentQty = $productData->qty;
                        $updatedQty = $currentQty-$item->qty;
                        $productData->qty = $updatedQty;
                        $productData->save();
                    }

                }

                // Send Order Email
                orderEmail($order->id,'customer');

                Session()->flash('success','You have Successfully place your order.');

                // After purchasing order cart will be empty
                Cart::destroy();

                Session()->forget('code');

                return response()->json([
                    'message' => 'Order Saved Successfully.',
                    'orderId' => $order->id,
                    'status' => true,
                ]);

            }

    }

    public function thankyou($id)
    {
        return view('front.thankyou',[
            'id' => $id,
        ]);
    }


    // When user change its country location shipping charges will be update on the spot
    public function getOrderSummery(Request $request)
    {
        $subTotal = Cart::subtotal(2,'.','');
        $discount = 0;
        $discontString = '';

        // Apply Dicount here
        if(session()->has('code')){
           $code = session()->get('code');
            if($code->type == 'percent'){
                $discount = ($code->discount_amount/100)*$subTotal;
            }
            else{
                $discount = $code->discount_amount;
            }

            $discontString =  '<div class="mt-4" id="discount-response">
            <strong> '.session()->get('code')->code.' </strong>
            <a class="btn btn-sm btn-danger" id="remove-discount"><i class="fa fa-times"></i></a>
            </div>';

        }


        if($request->country_id > 0){

            $shippingInfo = ShippingCharge::where('country_id',$request->country_id)->first();

            $totalQty = 0;
            foreach (Cart::content() as $item) {
                $totalQty += $item->qty;
            }

            if($shippingInfo != null)
            {
                $shippingCharge = $totalQty * $shippingInfo->amount;
                $grandTotal = ($subTotal - $discount) + $shippingCharge;

                return response()->json([
                    'status' => true,
                    'grandTotal' => number_format($grandTotal,2),
                    'discount' => number_format($discount,2),
                    'shippingCharge' => number_format($shippingCharge,2),
                    'discontString' => $discontString,
                ]);
            }
            else{
            // When user select country and that country is not availible in our list then we will charge the rest of the worlds amount
            $shippingInfo = ShippingCharge::where('country_id','rest_of_world')->first();
            $shippingCharge = $totalQty * $shippingInfo->amount;
            $grandTotal = ($subTotal - $discount) + $shippingCharge;

            return response()->json([
                'status' => true,
                'grandTotal' => number_format($grandTotal,2),
                'discount' => number_format($discount,2),
                'shippingCharge' => number_format($shippingCharge,2),
                'discontString' => $discontString,
            ]);
            }
        }
        else{
            // When Country ID is selected blank like he choose select a country
            return response()->json([
                'status' => true,
                'grandTotal' => number_format(($subTotal - $discount),2),
                'discount' => number_format($discount,2),
                'shippingCharge' => number_format(0,2),
                'discontString' => $discontString,
            ]);
        }
    }

    // Apply Discount function
    public function applyDiscount(Request $request){

        $code = DiscountCoupon::where('code',$request->code)->first();

        if($code == null){
            return response()->json([
                'status' => false,
                'message' => 'Invalid discount coupon',
            ]);
        }


        // Check if coupon start date is valid or not
        $now = Carbon::now();
        // echo $now->format('Y-m-d H:i:s');

        // Start date condition
        if($code->starts_at != ""){
            $startDate = Carbon::createFromFormat('Y-m-d H:i:s',$code->starts_at);
            if($now->lt($startDate))
            {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid discount coupon11',
                ]);
            }
        }

        // Expire date condition
        if($code->expires_at != ""){
            $endDate = Carbon::createFromFormat('Y-m-d H:i:s',$code->expires_at);
            if($now->gt($endDate))
            {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid discount coupon2',
                ]);
            }
        }

        // Max Uses Check
        if($code->max_uses > 0){

            $couponUsed = Order::where('coupon_code_id',$code->id)->count();
            if($couponUsed >= $code->max_uses){
                return response()->json([
                    'status' => false,
                    'message' => "Invalid discount coupon",
                ]);
            }
        }

        // Max Uses user check
        if($code->max_uses_user > 0){

            $couponUsedByUser = Order::where(['coupon_code_id' => $code->id, 'user_id' => Auth::user()->id])->count();

            if ($couponUsedByUser >= $code->max_uses_user) {
                return response()->json([
                    'status' => false,
                    'message' => 'You have already used this coupon code.',
                ]);
            }
        }
        // The code continues here if the user is eligible to use the coupon.

        $subTotal = Cart::subtotal(2,'.','');

        // Minimum Amount condition Check
        if($code->min_amount > 0)
        {
            if($subTotal < $code->min_amount)
            {
                return response()->json([
                    'status' => false,
                    'message' => 'Your min amount must be PKR: '.$code->min_amount.'.',
                ]);
            }
        }

        session()->put('code',$code);
        return $this->getOrderSummery($request);
    }

    public function removeCoupon(Request $request)
    {
        Session()->forget('code');
        return $this->getOrderSummery($request);
    }
}

