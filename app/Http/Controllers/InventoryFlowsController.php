<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use App\Inventory_flows;
use App\User;
use App\Inventory;
use App\Http\Controllers\Messages;

class InventoryFlowsController extends Controller
{
    protected $msg;

    public function __construct()
    {
      $this->msg = new Messages();
    }

    public function index(){
        $data = Inventory_flows::all();
        return response($data);
    }

    public function insert(Request $request){
        $user = User::where('uuid', $request->input('user_uuid'))->first();
        if(!$user){
            return response()->json($this->msg->user_not_found,406);
        }
        $inventory = Inventory::where('uuid',$request->input('inventory_uuid'))->first();
        if(!$inventory){
            return response()->json($this->msg->product_not_found,406);
        }
        if($request->input('changer')==0 && $inventory->quantity<$request->input('quantity')){
            return response()->json(["inventory"=>"Input quantity product can't more than quantity product in database"],406);
        }
        $validator = Validator::make($request->all(), [
            'quantity' => ['required'],
            'changer_name' => ['required','max:50'],
            'changer' => ['required']
        ]);
        if ($validator->fails()) {
            return response()->json( $validator->errors(),406);
        }

        $data = new Inventory_flows();
        $data->uuid = Str::uuid()->toString();
        $data->user_id = $user->id;
        $data->inventory_id = $inventory->id;
        $data->quantity = $request->input('quantity');
        $data->changer_name = $request->input('changer_name');
        $data->changer = $request->input('changer');
        try {
            $data->save();
            return response()->json('Insert data successful',201);
        } 
        catch (QueryException $e){
            $message = $e->getMessage();
            return response()->json($message,422);
        }
    }

    public function showBuyer(Request $request){
        $input_search_buyer = $request->input_search_buyer;
        if(!isset($input_search_buyer)){
            $buyer = DB::table('inventory')
            ->join('inventory_flows', 'inventory_flows.inventory_id', '=', 'inventory.id')
            ->join('users', 'inventory_flows.user_id', '=', 'users.id')
            ->select('product_name', 'product','inventory_flows.quantity','fullname','email','changer_name','inventory_flows.updated_at')
            ->where('inventory_flows.changer', 0)
            ->orderByRaw('inventory_flows.updated_at DESC')
            ->paginate(10);
        }
        else{       
            $buyer = DB::table('inventory')
            ->join('inventory_flows', 'inventory_flows.inventory_id', '=', 'inventory.id')
            ->join('users', 'inventory_flows.user_id', '=', 'users.id')
            ->select('product_name', 'product','inventory_flows.quantity','fullname','email','changer_name','inventory_flows.updated_at')
            ->where('inventory_flows.changer', 0)
            ->where('product_name','like',"%".$input_search_buyer."%")
            ->orderByRaw('inventory_flows.updated_at DESC')
            ->paginate(10);
        }
        if(!$buyer->isEmpty()){
            return response()->json($buyer,200);
        }
        else{
            return response()->json($buyer,203);
        }
    }

    public function showSupplier(Request $request){
        $input_search_supplier = $request->input_search_supplier;
        if(!isset($input_search_supplier)){
            $supplier = DB::table('inventory')
            ->join('inventory_flows', 'inventory_flows.inventory_id', '=', 'inventory.id')
            ->join('users', 'inventory_flows.user_id', '=', 'users.id')
            ->select('product_name', 'product','inventory_flows.quantity','fullname','email','changer_name','inventory_flows.updated_at')
            ->where('inventory_flows.changer', 1)
            ->orderByRaw('inventory_flows.updated_at DESC')
            ->paginate(10);
        }
        else{       
            $supplier = DB::table('inventory')
            ->join('inventory_flows', 'inventory_flows.inventory_id', '=', 'inventory.id')
            ->join('users', 'inventory_flows.user_id', '=', 'users.id')
            ->select('product_name', 'product','inventory_flows.quantity','fullname','email','changer_name','inventory_flows.updated_at')
            ->where('inventory_flows.changer', 1)
            ->where('product_name','like',"%".$input_search_supplier."%")
            ->orderByRaw('inventory_flows.updated_at DESC')
            ->paginate(10);
        }

        if(!$supplier->isEmpty()){
            return response()->json($supplier,200);
        }
        else{
            return response()->json($supplier,203);
        }
    }

    public function showDetailProduct(Request $request){
        $product = DB::table('inventory')
        ->leftJoin('inventory_flows', 'inventory_flows.inventory_id', '=', 'inventory.id')
        ->select('inventory_flows.quantity','inventory_flows.changer_name','inventory_flows.changer')
        ->where('inventory.uuid', $request->uuid)
        ->orderByRaw('inventory_flows.updated_at DESC')
        ->get();
        
        if(!$product->isEmpty()){
            return response()->json($product,200);
        }
    }
}
