<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Tank;
use App\TankTransaction;
use App\TankChild;
use App\Product;
use App\Business;
use DB;

class TankTransactionController extends Controller
{
   
    
    public function index()
    {
        $data  = TankTransaction::All();
        return view('tank_transaction.index')->with(compact('data'));
    }
    
    public function create()
    {   
        $prefix = 'TTR-';
        $trans_no = Tank::where('code', 'like', '%' . $prefix . '%')
            ->select('code', DB::raw("substring_index(substring_index(code,'-',-1),',',-1) as max_no"))->get()
            ->max('max_no');

        if (empty($trans_no)) {
            $code = 1;
        } else {
            $break_no = explode("-", $trans_no);
            $code = end($break_no) + 1;
        }
        $tran_no = $prefix . str_pad($code, 4, '0', STR_PAD_LEFT);

        $stores     = DB::table('store')->get();
        $tanks      = DB::table('tank_rate')->get();
        $product_type_id   = DB::table('default_account')->where('form_type','tank')->where('field_type','product_type')->first()->account_id;
        $products   = DB::table('products')->where('product_type', $product_type_id)->get();
        return view('tank_transaction.create')->with(compact('stores','tanks','products','tran_no'));
    }

    public function store(Request $request){
        try {
            DB::beginTransaction();
                $input = [];
                $input['ref_no']            = $request->input('ref_no');
                $input['date']              = $request->input('date');
                $input['comments']          = $request->input('comments');
                $tank_tr = TankTransaction::create($input);
                
                foreach($request->tank as $key => $val){
                    $TankChild                      = new TankChild;
                    $TankChild->tank_transaction_id = $tank_tr->id;
                    $TankChild->store               = (isset($request->store[$key]) ? $request->store[$key] : null);
                    $TankChild->tank                = (isset($request->tank[$key]) ? $request->tank[$key] : null);
                    $TankChild->product_id          = (isset($request->product[$key]) ? $request->product[$key] : null);
                    $TankChild->date                = (isset($input['date']) ? $input['date'] : null);
                    $TankChild->calibration         = (isset($request->calibration[$key]) ? $request->calibration[$key] : null);
                    $TankChild->quantity            = (isset($request->quantity[$key]) ? $request->quantity[$key] : null);
                    $TankChild->save();
                }
                
            DB::commit();
            $output = ['msg' => 'success'];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("Filed:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = ['msg' => 'error'];
        }
        return redirect()->action('TankTransactionController@index')->with($output);
    }

    public function show(Request $request){

        $data = Product::join('tank_child','products.id','=','tank_child.product_id')
                    ->where('tank_child.tank_transaction_id',request()->id)
                    ->select(
                        'products.name',
                        'tank_child.*',
                        DB::raw('SUM(tank_child.quantity) as total_quantity'),
                        DB::raw('SUM(tank_child.calibration) as total_calibration')
                    )
                    ->groupby('tank_child.product_id')
                    ->get();
                    
        if(request()->isonlysummary){
            return view('tank_transaction.summary')->with(compact('data'));
        }
        return view('tank_transaction.show')->with(compact('data'));
        
    }

    public function edit($id){
        $stores             = DB::table('store')->get();
        $tanks              = DB::table('tank_rate')->get();
        $product_type_id    = DB::table('default_account')->where('form_type','tank')->where('field_type','product_type')->first()->account_id;
        $products           = DB::table('products')->where('product_type', $product_type_id)->get();
        $tank_transaction   = TankTransaction::where('id',$id)->first();
        $tank_child         = TankChild::where('tank_transaction_id',$id)->get();
        return view('tank_transaction.edit')->with(compact('tank_transaction','tank_child','stores','tanks','products'));
    }

    public function update(Request $request){
        try {
            DB::beginTransaction();
                $id = $request->input('id');
                $input = [];
                $input['ref_no']            = $request->input('ref_no');
                $input['date']              = $request->input('date');
                $input['comments']          = $request->input('comments');
                $tank_tr = TankTransaction::where('id',$id)->update($input);
                
                $delete = TankChild::where('tank_transaction_id',$id)->delete();
                
                foreach($request->tank as $key => $val){
                    $TankChild                      = new TankChild;
                    $TankChild->tank_transaction_id = $id;
                    $TankChild->store               = (isset($request->store[$key]) ? $request->store[$key] : null);
                    $TankChild->tank                = (isset($request->tank[$key]) ? $request->tank[$key] : null);
                    $TankChild->product_id          = (isset($request->product[$key]) ? $request->product[$key] : null);
                    $TankChild->date                = (isset($input['date']) ? $input['date'] : null);
                    $TankChild->calibration         = (isset($request->calibration[$key]) ? $request->calibration[$key] : null);
                    $TankChild->quantity            = (isset($request->quantity[$key]) ? $request->quantity[$key] : null);
                    $TankChild->save();
                }
                

            DB::commit();
            $output = ['msg' => 'success'];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("Filed:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = ['msg' => 'error'];
        }
        return redirect()->action('TankTransactionController@index')->with($output);
    }
    
    public function destroy($id){
        try {
            DB::beginTransaction();
                
            $delete = TankTransaction::find($id)->delete();
            $delete_child = TankChild::where('tank_transaction_id',$id)->delete();
                
            DB::commit();
            $output = ['msg' => 'success'];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("Filed:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = ['msg' => 'error'];
        }
        return redirect()->back()->with($output);
    }
    
    
}
