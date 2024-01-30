<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::latest();

        if(!empty($request->get('keyword'))){
            $users = $users->where('name','like','%'.$request->get('keyword').'%');
            $users = $users->orWhere('email','like','%'.$request->get('keyword').'%');
        }

        $users = $users->paginate(10);
        return view('admin.users.list',[
            'users' => $users,
        ]);
    }

    public function create(Request $request){
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'required',
            'password' => 'required|min:5',

        ]);
        if($validator->passes()){
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->status = $request->status;
            $user->save();

            $msg = 'User added Successfully';
            Session()->flash('success',$msg);
            return response()->json([
                'status' => true,
                'message' => $msg,
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

    }


    public function edit(Request $request, $id)
    {
        $user = User::find($id);
        if(empty($user))
        {
            $message = 'User not Found';
            Session()->flash('error',$message);
            return redirect()->route('users.index');
        }

        return view('admin.users.edit',[
            'user'=> $user,
        ]);
    }


    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if(empty($user))
        {
            $message = 'User not Found';
            Session()->flash('error',$message);

            return response()->json([
                'status' => true,
                'message' => $message,
            ]);
        }

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id.',id',
            'phone' => 'required',
        ]);
        if($validator->passes()){

            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;

            if($request->password != ''){
                $user->password = Hash::make($request->password);
            }
            $user->status = $request->status;

            $user->save();

            $msg = 'User added Successfully';
            Session()->flash('success',$msg);
            return response()->json([
                'status' => true,
                'message' => $msg,
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

    }

    public function destroy($id)
    {
        $user = User::find($id);
        if(empty($user))
        {
            $message = 'User not found';
            Session()->flash('error',$message);
            return response([
                'status'=> true,
                'message' => $message,
            ]);
        }

        $user->delete();

        $message = 'User deleted Successfully';
        Session()->flash('success',$message);

        return response([
            'status'=> true,
            'message' => $message,
        ]);

    }

}
