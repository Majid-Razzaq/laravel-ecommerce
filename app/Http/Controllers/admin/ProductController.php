<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\brand;
use App\Models\category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductRating;
use App\Models\SubCategory;
use App\Models\tempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::latest('id')->with('product_images');
        if($request->get('keyword') != "")
        {
            $products = $products->where('title','like','%'.$request->keyword.'%');
        }
        $products = $products->paginate();
        // dd($products);
        $data['products'] = $products;
        return view('admin.products.list',$data);
    }


    public function create()
    {
        $categories = category::orderBy('name','ASC')->get();
        $brands = brand::orderBy('name','ASC')->get();
        $data['categories'] = $categories;
        $data['brands'] = $brands;

        return view('admin.products.create',$data);
    }

    public function store(Request $request)
    {

        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:Yes,No',
       ];
    //    If track Quantity check is not empty
    if(!empty($request->track_qty) && $request->track_qty == 'Yes')
    {
        $rules['qty'] = 'required|numeric';
    }
       $validator = Validator::make($request->all(),$rules);

       if($validator->passes())
       {
            $product = new Product;
            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->status = $request->status;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brand;
            $product->is_featured = $request->is_featured;
            $product->short_description = $request->short_description;
            $product->shipping_returns = $request->shipping_returns;
            $product->related_products = (!empty($request->related_products)) ? implode(',',$request->related_products) : '';
            $product->save();

            // Save Gallery pics
            if(!empty($request->image_array)){
                foreach ($request->image_array as $temp_image_id) {

                    $tempImageInfo = tempImage::find($temp_image_id);
                    $extArray = explode('.',$tempImageInfo->name);
                    $ext = last($extArray);//like jpg, gif,png

                    $productImage = new ProductImage();
                    $productImage->product_id = $product->id;
                    $productImage->image = 'NULL';
                    $productImage->Save();

                    $imageName = $product->id.'-'.$productImage->id.'-'.time().'.'.$ext;
                    $productImage->image = $imageName;
                    $productImage->save();

                    // Generate Thumbnails

                    // Large Image
                    $sourcePath = public_path().'/temp/'.$tempImageInfo->name;
                    $destPath = public_path().'/uploads/product/large/'.$imageName;
                    $image = Image::make($sourcePath);
                    $image->resize(1400, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $image->save($destPath);


                    // Small Image
                    $destPath = public_path().'/uploads/product/small/'.$imageName;
                    $image = Image::make($sourcePath);
                    $image->fit(300, 300);
                    $image->save($destPath);

                }
            }

            Session()->flash('success','Product added successfully');
            return response()->json([
                'status' => true,
                'message' => 'Product added successfully',
            ]);
       }
       else{
        return response()->json([
            'status' => false,
            'errors' => $validator->errors(),
        ]);
       }

    }


    public function edit($id, Request $request)
    {
        $product = Product::find($id);

        if(empty($product))
        {
            return redirect()->route('products.index')->with('error','Product not found');
        }
        // Fetch product Image
        $productImages = ProductImage::where('product_id',$product->id)->get();

        // select subCategory name using prodcut SubCategory id
        $subCategories = SubCategory::where('category_id',$product->category_id)->get();

        // Fetch Related products
        $relatedProducts = [];
        if($product->related_products != '')
        {
            $productArray = explode(',',$product->related_products);
            $relatedProducts = Product::whereIn('id',$productArray)->get();
        }

        $data = [];
        $categories = category::orderBy('name','ASC')->get();
        $brands = brand::orderBy('name','ASC')->get();
        $data['categories'] = $categories;
        $data['brands'] = $brands;
        $data['product'] = $product;
        $data['subCategories'] = $subCategories;
        $data['productImages'] = $productImages;
        $data['relatedProducts'] = $relatedProducts;
        return view('admin.products.edit',$data);
    }

    // update method
    public function update($id, Request $request)
    {
        $product = Product::find($id);
        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products,slug,'.$product->id.',id',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products,sku,'.$product->id.',id',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:Yes,No',
       ];
    //    If track Quantity check is not empty
    if(!empty($request->track_qty) && $request->track_qty == 'Yes')
    {
        $rules['qty'] = 'required|numeric';
    }
       $validator = Validator::make($request->all(),$rules);

       if($validator->passes())
       {
            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->status = $request->status;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brand;
            $product->is_featured = $request->is_featured;
            $product->short_description = $request->short_description;
            $product->shipping_returns = $request->shipping_returns;
            $product->related_products = (!empty($request->related_products)) ? implode(',',$request->related_products) : '';
            $product->save();

            Session()->flash('success','Product updated successfully');
            return response()->json([
                'status' => true,
                'message' => 'Product updated successfully',
            ]);
       }
       else{
        return response()->json([
            'status' => false,
            'errors' => $validator->errors(),
        ]);
       }
    }


    // Product delete function
    public function destroy($id,Request $request)
    {
        $product = Product::find($id);

        if(empty($product))
        {
            Session()->flash('error','Product not found');
            return response()->json([
                'status' => false,
                'notFound' => true,
            ]);
        }

        $productImages = ProductImage::where('product_id',$id)->get();

        if(!empty($productImages))
        {
            foreach ($productImages as $productImage) {
                File::delete(public_path('uploads/product/large/'.$productImage->image));
                File::delete(public_path('uploads/product/small/'.$productImage->image));
            }

            productImage::where('product_id',$id)->delete();
        }

        $product->delete();

        Session()->flash('success','Product deleted Successfully');
        return response()->json([
            'status'=> true,
            'message'=> 'Product deleted Successfully',
        ]);


    }

    public function getProducts(Request $request)
    {
        $tempProduct = [];
        if($request->term != "")
        {
            $products = Product::where('title','like','%'.$request->term.'%')->get();

            if($products != null)
            {
                foreach ($products as $product) {
                    $tempProduct[] = array('id' => $product->id, 'text' => $product->title);
                }
            }
        }

        // print_r($tempProduct);
        return response()->json([
            'tags' => $tempProduct,
            'status' => true,
        ]);

    }

    // Rating Methods
    public function productRatings(Request $request){


        $ratings = ProductRating::select('product_ratings.*','products.title as productTitle')->orderBy('product_ratings.created_at','DESC');
        $ratings = $ratings->leftJoin('products','products.id','product_ratings.product_id');

        if($request->get('keyword') != "")
        {
            $ratings = $ratings->orWhere('products.title','like','%'.$request->keyword.'%');
            $ratings = $ratings->orWhere('product_ratings.username','like','%'.$request->keyword.'%');
        }

        $ratings = $ratings->paginate(10);
        return view('admin.products.ratings',[

            'ratings' => $ratings,
        ]);
    }

    public function changeRatingStatus(Request $request){

        $productRating = ProductRating::find($request->id);
        $productRating->status = $request->status;
        $productRating->save();

        Session()->flash('success','Status change successfully.');

        return response()->json([
            'status' => true,

        ]);

    }

    // delete Rating method
    public function destroyRating($id){

        $rating = ProductRating::find($id);

        if(empty($rating)){
            Session()->flash('error','Rating not found');
            return response()->json([
                'status' => false,
                'notFound' => true,
            ]);
        }

        $rating->delete();

        Session()->flash('success','Rating deleted successfully');
        return response()->json([
            'status' => true,
            'message' => 'Rating deleted Successfully',
        ]);


    }
}


