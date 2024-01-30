<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $pages = Page::latest();
        if($request->keyword != '')
        {
            $pages = Page::where('name','like','%'.$request->keyword.'%');
        }
        $pages = $pages->paginate(10);
        $data['pages'] = $pages;
        return view('admin.pages.list',$data);
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

        $page = new Page;
        $page->name = $request->name;
        $page->slug = $request->slug;
        $page->content = $request->content;
        $page->save();

        $message = 'Page added successfully';
        Session()->flash('success',$message);
        return response()->json([
            'status' => true,
            'errors' => $message,
        ]);

    }

    public function edit($id)
    {
        $page = Page::find($id);
        if(empty($page))
        {
            return redirect()->route('pages.index');
        }
        return view('admin.pages.edit',[
            'page' => $page,
        ]);
    }

    public function update(Request $request, $pageId)
    {
        $page = Page::find($pageId);
        if(empty($page))
        {
            $message = 'Page not found';
            Session()->flash('error',$message);

            return response()->json([
                'status' => true,
                'message' => $message,
            ]);
        }

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required',
        ]);
        if($validator->passes())
        {
            $page->name = $request->name;
            $page->slug = $request->slug;
            $page->content = $request->content;
            $page->save();

            $message = 'Page updated successfully';
            Session()->flash('success',$message);
            return response()->json([
                'status' => true,
                'message' => $message,
            ]);
        }
        else
        {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function destroy($id)
    {
        $page = Page::find($id);
        if(empty($page))
        {
            $message = 'Page not found';
            Session()->flash('error',$message);
            return response()->json([
                'status' => true,
                'message' => $message,
            ]);
        }

        $page->delete();

        $message = 'Page deleted successfully';
        Session()->flash('success',$message);
        return response()->json([
            'status' => true,
            'message' => $message,
        ]);

    }
}
