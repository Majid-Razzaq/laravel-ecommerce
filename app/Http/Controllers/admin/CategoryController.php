<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\category;
use App\Models\tempImage;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = category::latest();
        if(!empty($request->get('keyword')))
        {
            $categories = category::where('name','like','%'.$request->get('keyword').'%');
        }
        $categories = $categories->paginate(10);
        $data['categories'] = $categories;
        return view('admin.category.list',$data);
    }

    public function create()
    {
        return view('admin.category.create');

    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:categories',
        ]);

        if($validator->passes()){

            $category = new category();
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->showHome = $request->showHome;

            $category->save();


                // save image here
                if(!empty($request->image_id))
                {
                    $tempImage = tempImage::find($request->image_id);
                    $extArray = explode('.',$tempImage->name);
                    $ext = last($extArray);

                    $newImageName = $category->id.'-'.time().'.'.$ext;
                    $sPath = public_path().'/temp/'.$tempImage->name;
                    $dPath = public_path().'/uploads/category/'.$newImageName;

                    File::copy($sPath,$dPath);

                    // Generate Image Thumbnail
                    $dPath = public_path().'/uploads/category/thumb/'.$newImageName;
                    $img = Image::make($sPath);
                    //$img->resize(450,600);
                    $img->fit(450, 600, function ($constraint) {
                        $constraint->upsize();
                    });
                    $img->save($dPath);

                    $category->image = $newImageName;
                    $category->save();

                }


            Session()->flash('success','Category addedd Successfully.');

            return response()->json([
                'status' => true,
                'message' => 'Category addedd Successfully.',
            ]);
        }
        else
        {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }


    public function edit($categoryId, Request $request)
    {
        $category = category::find($categoryId);
        if(empty($category))
        {
            return redirect()->route('categories.index');
        }

        return view('admin.category.edit',compact('category'));
    }



    // Update method
    public function update(Request $request,$categoryId)
    {

        $category = category::find($categoryId);
        if(empty($category))
        {
            Session()->flash('error','Category not Found');
            return response()->json([
                'status' => false,
                'notFound' => 'true',
                'message' => 'Category not found',
            ]);
        }

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,'.$category->id.',id',
        ]);

        if($validator->passes()){

            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->showHome = $request->showHome;

            $category->save();

            // Delete Old Image
            $olgImage = $category->image;

                // save image here
                if(!empty($request->image_id))
                {
                    $tempImage = tempImage::find($request->image_id);
                    $extArray = explode('.',$tempImage->name);
                    $ext = last($extArray);

                    $newImageName = $category->id.'.'.$ext;
                    $sPath = public_path().'/temp/'.$tempImage->name;
                    $dPath = public_path().'/uploads/category/'.$newImageName;

                    File::copy($sPath,$dPath);

                    // Generate Image Thumbnail
                    $dPath = public_path().'/uploads/category/thumb/'.$newImageName;
                    $img = Image::make($sPath);
                    //$img->resize(450,600);
                    $img->fit(450, 600, function ($constraint) {
                        $constraint->upsize();
                    });
                    $img->save($dPath);

                    $category->image = $newImageName;
                    $category->save();

                    // Delete Old Images here
                    File::delete(public_path().'/uploads/category/thumb/'.$olgImage);
                    File::delete(public_path().'/uploads/category/'.$olgImage);

                }

            Session()->flash('success','Category updated Successfully.');

            return response()->json([
                'status' => true,
                'message' => 'Category added Successfully.',
            ]);
        }
        else
        {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy($categoryId, Request $request)
    {
        $category = category::find($categoryId);
        if(empty($category))
        {
            Session()->flash('error','Category not found');
            return response([
                'status'=> true,
                'message' => 'Category not found',
            ]);
        }

        //Delete Old Images here
        File::delete(public_path().'/uploads/category/thumb/'.$category->image);
        File::delete(public_path().'/uploads/category/'.$category->image);

        $category->delete();

        Session()->flash('success','Category deleted Successfully');

        return response([
            'status'=> true,
            'message' => 'Category deleted Successfully',
        ]);


    }
}
