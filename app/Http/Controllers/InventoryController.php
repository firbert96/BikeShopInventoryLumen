<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use App\User;
use App\Inventory;
use App\Inventory_flows;
use App\Http\Controllers\Messages;

class InventoryController extends Controller
{
    protected $msg;

    public function __construct()
    {
      $this->msg = new Messages();
    }

    public function index(){
        $data = Inventory::all();
        return response($data);
    }

    public function insert(Request $request){
        $data = new Inventory();
        $user = User::where('uuid', $request->input('user_uuid'))->first();
        if(!$user){
            return response()->json($this->msg->user_not_found,406);
        }
        $validator = Validator::make($request->all(), [
            'product_name' => ['required','max:50'],
            'product' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json( $validator->errors(),406);
        }
        $data->uuid = Str::uuid()->toString();
        $data->user_id = $user->id;
        $data->product_name = $request->input('product_name');
        $data->product = $request->input('product');
        $data->quantity = 0;
        try {
            $data->save();
            return response()->json('Insert data successful',201);
        } 
        catch (QueryException $e){
            $message = $e->getMessage();
            return response()->json($message,422);
        }
    }

    public function editProduct(Request $request){
        $user = User::where('uuid', $request->input('user_uuid'))->first();
        if(!$user){
            return response()->json($this->msg->user_not_found,406);
        }
        $data = Inventory::where('uuid',$request->input('uuid'))->first();
        if(!$data){
            return response()->json($this->msg->product_not_found,406);
        }
        $validator = Validator::make($request->all(), [
            'product_name' => ['required','max:50'],
            'product' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json( $validator->errors(),406);
        }
        $data->user_id = $user->id;
        $data->product_name = $request->input('product_name');
        $data->product = $request->input('product');
        $data->quantity = 0;
        try {
            $data->save();
            return response()->json('Update product successful',200);
        } 
        catch (QueryException $e){
            $message = $e->getMessage();
            return response()->json($message,422);
        }
    }

    public function editQuantity(Request $request){
        $user = User::where('uuid', $request->input('user_uuid'))->first();
        if(!$user){
            return response()->json($this->msg->user_not_found,406);
        }
        $data = Inventory::where('uuid',$request->input('inventory_uuid'))->first();
        if(!$data){
            return response()->json($this->msg->product_not_found,406);
        }
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|numeric|min:0|not_in:0'
        ]);
        if ($validator->fails()) {
            return response()->json( $validator->errors(),406);
        }

        if($request->input('changer')==0 && $data->quantity<$request->input('quantity')){
            return response()->json(["inventory"=>"Input quantity product can't more than quantity product in database"],406);
        }
        if($request->input('changer')==0){
            $quantity=$data->quantity-$request->input('quantity');
        }
        else{
            $quantity=$data->quantity+$request->input('quantity');
        }
        $data->user_id = $user->id;
        $data->quantity = $quantity;
        try {
            $data->save();
            return response()->json('Update quantity product successful',200);
        } 
        catch (QueryException $e){
            $message = $e->getMessage();
            return response()->json($message,422);
        }
    }

    public function delete(Request $request){
        $data = Inventory::where('uuid',$request->uuid)->first();
        try {
            $data->delete();
            return response()->json('Delete data successful',200);
        } 
        catch (QueryException $e){
            $message = $e->getMessage();
            return response()->json($message,422);
        }
    }

    public function showInventory(Request $request){     
        $input_search = $request->input_search;
        if(!isset($input_search)){
            $inventory = DB::table('inventory')
            ->join('users', 'inventory.user_id', '=', 'users.id')
            ->select('inventory.uuid','product_name', 'product','inventory.quantity','fullname','email','inventory.updated_at')
            ->orderByRaw('updated_at DESC')
            ->paginate(10);
        }
        else{       
            $inventory = DB::table('inventory')
            ->join('users', 'inventory.user_id', '=', 'users.id')
            ->select('inventory.uuid','product_name', 'product','inventory.quantity','fullname','email','inventory.updated_at')
            ->where('product_name','like',"%".$input_search."%")
            ->orderByRaw('updated_at DESC')
            ->paginate(10);
        }

        if(!$inventory->isEmpty()){
            return response()->json($inventory,200);
        }
        else{
            return response()->json($inventory,203);
        }
    }

    public function showDetailProduct(){
        $product = DB::table('inventory')
        ->join('users', 'inventory.user_id', '=', 'users.id')
        ->select('inventory.uuid','product_name', 'product','inventory.quantity','fullname','email','inventory.updated_at')
        ->where('inventory.quantity','>', 0)
        ->orderByRaw('inventory.product_name ASC')
        ->get();
        
        if(!$product->isEmpty()){
            return response()->json($product,200);
        }
    }
}
