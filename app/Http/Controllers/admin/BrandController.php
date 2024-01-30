<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Expr\FuncCall;

class BrandController extends Controller
{

    public function index(Request $request)
    {
        $brands = brand::latest('id');

        if($request->get('keyword'))
        {
            $brands = $brands->where('name','like','%'.$request->keyword.'%');
        }

        $brands = $brands->paginate(10);

        return view('admin.brands.list',compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'=> 'required',
            'slug'=> 'required|unique:brands',
        ]);

        if($validator->passes())
        {
            $brand = new brand();
            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;
            $brand->save();
            Session()->flash('success','Brand inserted Successfully.');

            return response()->json([
                'status' => true,
                'message' => 'Brand inserted successfully',
            ]);
        }
        else{
            return response([
                'status' => 'false',
                'errors' => $validator->errors(),
            ]);
        }
    }

    // Edit function
    public function edit($id, Request $request)
    {
        $brand = brand::find($id);
        if(empty($brand))
        {
            Session()->flash('error','Record not found');
            return redirect()->route('brands.index');
        }

        $data['brand'] = $brand;
        return view('admin.brands.edit',$data);
    }

    // update function
    public function update(Request $request, $id)
    {
        $brand = brand::find($id);
        if(empty($brand))
        {
            Session()->flash('error','Record not found.');
            return response()->json([
                'status' => false,
                'notFound' => true,
            ]);
        }

        $validator = Validator::make($request->all(),[
            'name'=> 'required',
            'slug'=> 'required|unique:brands,slug,'.$brand->id.',id',
        ]);

        if($validator->passes())
        {
            $brand = new brand();
            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;
            $brand->save();


            Session()->flash('success','Brand updated Successfully.');

            return response()->json([
                'status' => true,
                'message' => 'Brand updated successfully',
            ]);
        }
        else{
            return response([
                'status' => 'false',
                'errors' => $validator->errors(),
            ]);
        }
        // return view('admin.brands.create');
    }


    public function destroy($id)
    {
        $brand = brand::find($id);
        if(empty($brand))
        {
            Session()->flash('error','Brand not found');
            return response([
                'status'=> true,
                'message' => 'Brand not found',
            ]);
        }

        $brand->delete();

        Session()->flash('success','Brand deleted Successfully');

        return response([
            'status'=> true,
            'message' => 'Brand deleted Successfully',
        ]);


    }

}
