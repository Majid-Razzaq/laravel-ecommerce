<?php

namespace App\Http\Controllers;

use App\Models\brand;
use App\Models\category;
use App\Models\Product;
use App\Models\ProductRating;
use App\Models\SubCategory;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShopController extends Controller
{
    public function index(Request $request, $categorySlug = null, $subCategorySlug = null)
    {

        $categorySelected = '';
        $subCategorySelected = '';
        $brandsArray = [];


        $categories = category::orderBy('name','ASC')
        ->with('sub_category')
        ->where('status',1)
        ->get();

        // Fetch brand
        $brands = brand::orderBy('name','ASC')
        ->where('status',1)
        ->get();

        // Fetch Products and filters
        $products = Product::where('status',1);

        // Apply filters here
        // for Category
        if(!empty($categorySlug))
        {
            $category = category::where('slug',$categorySlug)->first();
            $products = $products->where('category_id',$category->id);
            $categorySelected = $category->id;
        }

        // For SubCategory
        if(!empty($subCategorySlug))
        {
            $subCategory = SubCategory::where('slug',$subCategorySlug)->first();
            $products = $products->where('sub_category_id',$subCategory->id);
            $subCategorySelected = $subCategory->id;
        }


        if(!empty($request->get('brand'))){
            $brandsArray = explode(',',$request->get('brand'));
            $products = $products->whereIn('brand_id',$brandsArray);
        }

        // get Filter values
        if ($request->get('price_max') != '' && $request->get('price_min') != '') {

            if($request->get('price_max') == 1000)
            {
                $products = $products->whereBetween('price', [intval($request->get('price_min')),1000000]);
            }
            else{
                $products = $products->whereBetween('price', [intval($request->get('price_min')), intval($request->get('price_max'))]);
            }
        }


        // Search Code
        if(!empty($request->get('search')))
        {
            $products = $products->where('title','like','%'.$request->get('search').'%');

        }


        // Sorting filter
        if($request->get('sort') != '')
        {
            if($request->get('sort') == 'latest')
            {
                $products = $products->orderBy('id','DESC');
            }
            else if($request->get('sort') == 'price_asc')
            {
                $products = $products->orderBy('price','ASC');
            }
            else
            {
                $products = $products->orderBy('price','DESC');
            }
        }
        else
        {
            $products = $products->orderBy('id','DESC');
        }

        $products = $products->paginate(6);

        $data['categories'] = $categories;
        $data['brands'] = $brands;
        $data['products'] = $products;
        $data['categorySelected'] = $categorySelected;
        $data['subCategorySelected'] = $subCategorySelected;
        $data['brandsArray'] = $brandsArray;
        $data['priceMax'] = (intval($request->get('price_max')) == 0) ? 1000 : $request->get('price_max');
        $data['priceMin'] = intval($request->get('price_min'));
        $data['sort'] = $request->get('sort');

        return view('front.shop',$data);
    }

    public function product($slug)
    {
        $product = product::where('slug',$slug)
                    ->withCount('product_ratings')
                    ->withSum('product_ratings','rating')
                    ->with('product_images','product_ratings')->first();

        if($product == null)
        {
            abort(404);
        }

         // Fetch Related products
         $relatedProducts = [];
         if($product->related_products != '')
         {
             $productArray = explode(',',$product->related_products);
             $relatedProducts = Product::whereIn('id',$productArray)->with('product_images')->where('status',1)->get();
         }

        $data['product'] = $product;
        $data['relatedProducts'] = $relatedProducts;

        //  Calculate AVG Rating here
        $avgRating = '0.00';
        $avgRatingPercentage = 0;
         if($product->product_ratings_count > 0){

            $avgRating = number_format(($product->product_ratings_sum_rating/$product->product_ratings_count),2);
            $avgRatingPercentage = ($avgRating*100)/5;
        }
         $data['avgRating'] = $avgRating;
         $data['avgRatingPer'] = $avgRatingPercentage;

        return view('front.product',$data);
    }

    public function saveRating($id, Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|min:5',
            'email' => 'required|email',
            'rating' => 'required',
            'comment' => 'required|min:10',
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

        $emailCount = ProductRating::where('email',$request->email)->count();
        if($emailCount > 0){

            Session()->flash('error','You already rated this product.');
            return response()->json([
                'status' => true,
            ]);
        }

        $productRating = new ProductRating;
        $productRating->product_id  = $id;
        $productRating->username = $request->name;
        $productRating->email = $request->email;
        $productRating->rating = $request->rating;
        $productRating->comment = $request->comment;
        $productRating->status = 0;
        $productRating->save();


        Session()->flash('success','Thank you for your rating.');

        return response()->json([
            'status' => true,
            'message' => 'Thank you for your rating.',
        ]);

    }


}
