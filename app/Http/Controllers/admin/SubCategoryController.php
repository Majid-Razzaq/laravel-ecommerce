<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{
    public function create()
    {
        $categories = category::orderBy('name','ASC')->get();
        $data['categories'] = $categories;
        return view('admin.sub_category.create',$data);
    }


    public function index(Request $request)
    {
        $categories = SubCategory::select('sub_categories.*','categories.name as categoryName')
                        ->latest('sub_categories.id')
                        ->leftJoin('categories','categories.id','sub_categories.category_id');

        if(!empty($request->get('keyword')))
        {
            // Search By subCategories name
            $categories = $categories->where('sub_categories.name','like','%'.$request->get('keyword').'%');
            // Search by category name
            $categories = $categories->orWhere('categories.name','like','%'.$request->get('keyword').'%');
        }

        $categories = $categories->paginate(10);
        $data['categories'] = $categories;
        return view('admin.sub_category.list',$data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:sub_categories',
            'status' => 'required',
            'category' => 'required',
        ]);

        if($validator->passes()){

            $sub_Category = new SubCategory();
            $sub_Category->name = $request->name;
            $sub_Category->slug = $request->slug;
            $sub_Category->status = $request->status;
            $sub_Category->showHome = $request->showHome;
            $sub_Category->category_id = $request->category;
            $sub_Category->save();

            Session()->flash('success','Sub Category created successfully');
            return response([
                'status' => true,
                'message' => 'Sub Category created successfully',
            ]);
        }
        else{
            return response([
                'status'=> false,
                'errors' => $validator->errors(),
            ]);
        }
    }

   //  Edit Function
    public function edit($SubCategoryId, Request $request)
    {
        $subCategory = SubCategory::find($SubCategoryId);
        if(empty($subCategory))
        {
            Session()->flash('error','Record not found');
            return redirect()->route('sub-categories.index');
        }
        $categories = category::orderBy('name','ASC')->get();
        $data['categories'] = $categories;
        $data['subCategory'] = $subCategory;
        return view('admin.sub_category.edit',$data);

    }

    // Update function
    public function update(Request $request,$SubCategoryId)
    {

        $sub_Category = SubCategory::find($SubCategoryId);
        if(empty($sub_Category))
        {
            Session()->flash('error','Record not found');
            return response([
                'status' => false,
                'notFound' => true,
            ]);
            // return redirect()->route('sub-categories.index');
        }

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:sub_categories,slug,'.$sub_Category->id.',id',
            'status' => 'required',
            'category' => 'required',
        ]);

        if($validator->passes()){

            $sub_Category->name = $request->name;
            $sub_Category->slug = $request->slug;
            $sub_Category->status = $request->status;
            $sub_Category->showHome = $request->showHome;
            $sub_Category->category_id = $request->category;
            $sub_Category->save();

            Session()->flash('success','Sub Category updated successfully');
            return response([
                'status' => true,
                'message' => 'Sub Category updated successfully',
            ]);
        }
        else{
            return response([
                'status'=> false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    // Delete Function
    public function destroy($subCategoryId, Request $request)
    {
        $sub_Category = SubCategory::find($subCategoryId);
        if(empty($sub_Category))
        {
            Session()->flash('error','Record not found');
            return response([
                'status' => false,
                'notFound' => true,
            ]);
        }

        $sub_Category->delete();
        Session()->flash('success','Sub Category deleted Successfully');

        return response([
            'status' => true,
            'message' => 'Sub Category deleted Successfully',
        ]);

    }


}
