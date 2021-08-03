<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Validator;

class UserController extends Controller
{
    public function Me(Request $request)
    {
         return response()->json( $request->user()->get(['id', 'name', 'email', 'username']));
    }

    public function EditUser(Request $request)
    {
   
        $user = $request->user();
        $data = $request->all();
        
        $validator = Validator::make($data,[
            'name' => 'string|nullable|max:255', 
            'username' => 'regex:/^[a-zA-Z]+$/u|max:100|unique:users',
        ]);
        
        if ($validator->fails()) {
          
            return response()->json([$validator->messages(), 'status' => 400], 200);
        }
      
        if(isset($data['name']) || isset($data['username'])){
           
           $user->update($data);
           $user->save();
       }
      
       if(!isset($data['name']) && !isset($data['username'])){
        return response()->json([
            'message' => 'Enter the name or username to update',
        ], 500);
    }

       return response()->json([
        'message' => 'User Updated Successfully'
    ], 200);

    }

    public function deleteUser(Request $request){
        $user = $request->user();

        $user->delete();

        return response()->json([
            'message' => "User Deleted Successfully"
        ], 200);
    }
}
