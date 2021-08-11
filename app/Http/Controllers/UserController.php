<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

use App\User;
use App\Http\Controllers\Messages;

class UserController extends Controller
{
    protected $msg;

    public function __construct()
    {
      $this->msg = new Messages();
    }

    public function index(){
        $data = User::all();
        return response($data);
    }

    public function insert(Request $request){
        $validator = Validator::make($request->all(), [
            'fullname' => ['required', 'max:50'],
            'email' => ['required','max:30','unique:users'],
            'phone' => ['required','max:15'],
        ]);

        if ($validator->fails()) {
            return response()->json( $validator->errors(),406);
        }

        $data = new User();
        $data->uuid = Str::uuid()->toString();
        $data->fullname = $request->input('fullname');
        $data->email = $request->input('email');
        $data->phone = $request->input('phone');
        $data->password = Hash::make($request->input('password'));
        try {
            $data->save();
            return response()->json('Register user successful',201);
        } 
        catch (QueryException $e){
            $message = $e->getMessage();
            return response()->json($message,422);
        }
    }

    public function edit(Request $request){
        $validator = Validator::make($request->all(), [
            'fullname' => ['required', 'max:50'],
            'email' => ['required','max:30'],
            'phone' => ['required','max:15'],
        ]);

        if ($validator->fails()) {
            return response()->json( $validator->errors(),406);
        }

        $data = User::where('uuid',$request->input('uuid'))->first();
        if(!$data){
            return response()->json($this->msg->user_not_found,406);
        }

        $checkEmail = User::where('email',$request->input('email'))->first();
        if($data->email!=$request->input('email') && $checkEmail){
            return response()->json($this->msg->email_taken,406);
        }

        $data->fullname = $request->input('fullname');
        $data->email = $request->input('email');
        $data->phone = $request->input('phone');
        try {
            $data->save();
            return response()->json('Update data successful',200);
        } 
        catch (QueryException $e){
            $message = $e->getMessage();
            return response()->json($message,422);
        }
    }

    public function changePassword(Request $request){
        $validator = Validator::make($request->all(), [
            'currentPassword' => ['required'],
            'newPassword' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json( $validator->errors(),406);
        }

        $data = User::where('uuid',$request->input('uuid'))->first();
        if(!$data){
            return response()->json($this->msg->user_not_found,406);
        }

        if (Hash::check($request->input('currentPassword'), $data->password)) {
            $data->password = Hash::make($request->input('newPassword'));
            try {
                $data->save();
                return response()->json('Change password user successful',200);
            } 
            catch (QueryException $e){
                $message = $e->getMessage();
                return response()->json($message,422);
            }
        }
        else{
            return response()->json(["password"=>"Input current password doesn't match with password user in database."],406);
        }
    }
}
