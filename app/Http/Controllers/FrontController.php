<?php

namespace App\Http\Controllers;

use App\Mail\ContactEmail;
use App\Models\Page;
use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Auth\Events\Validated;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class FrontController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::where('is_featured','Yes')
        ->orderBy('id','DESC')
        ->where('status',1)
        ->take(8)
        ->get();
        $data['featuredProducts'] = $products;

        // Wishlist data
        // $userId = Auth::user();
        // // Fetch the wishlist products for the authenticated user with the given product id.
        // $wishlistProducts = Wishlist::where('user_id', $userId->id)->get();
        // $data['wishlistProducts'] = $wishlistProducts;

        // LATEST PRODUSTS
        $latestProducts = Product::orderBy('id','DESC')
        ->where('status',1)
        ->take(8)
        ->get();
        $data['latestProducts'] = $latestProducts;

        return view('front.home',$data);
    }

    public function addToWishlist(Request $request)
    {

        Session(['url.intended' => url()->previous()]);
        if(Auth::check() == false){
            return response()->json([
                'status' => false,
            ]);
        }

        $product = Product::where('id',$request->id)->first();
        if($product == null){
            return response()->json([
                'status' => true,
                'message' => '<div class="alert alert-danger">Product not found.</div>',
            ]);

        }
        $wishlistproduct = Wishlist::where('user_id',Auth::user()->id)->where('product_id',$request->id)->first();
        if($wishlistproduct == null)
        {
            Wishlist::updateOrCreate(
                [
                    'user_id' => Auth::user()->id,
                    'product_id' => $request->id,
                ],
                [
                    'user_id' => Auth::user()->id,
                    'product_id' => $request->id,
                ]
            );

            // $wishlist = new Wishlist;
            // $wishlist->user_id = Auth::user()->id;
            // $wishlist->product_id = $request->id;
            // $wishlist->save();

            return response()->json([
                'status' => true,
                'message' => '<div class="alert alert-success"><strong>"'. $product->title .'"</strong> added in your wishlist</div>',
            ]);

        }
        else
        {
            return response()->json([
                'status' => true,
                'message' => '<div class="alert alert-danger"><strong>"'. $product->title .'"</strong> already added in your wishlist</div>',
            ]);

        }
    }

    public function page($slug)
    {
        $page = Page::where('slug',$slug)->first();
        if(empty($page))
        {
            abort(404);
        }
        return view('front.page',[
            'page' => $page,
        ]);
    }

    // Main send contact to admin
    public function sendContactEmail(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email',
            'subject' => 'required|min:10',
        ]);
        if($validator->passes()){
        // Send Email here
        $mailData = [
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'mail_subject' => 'You have received a contact email',
        ];

        $admin = User::where('id',1)->first();
        Mail::to($admin->email)->send(new ContactEmail($mailData));

        Session()->flash('success','Thanks for contacting us, we will get back to you soon.');
        return response()->json([
            'status' => true,
        ]);

        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

    }
}
