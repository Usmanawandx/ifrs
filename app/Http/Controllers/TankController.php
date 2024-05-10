<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Tank;
use App\TankTransaction;
use App\TankChild;
use DB;

class TankController extends Controller
{
   
    
    public function index()
    {
        $prefix = 'T-';
        $trans_no = Tank::where('code', 'like', '%' . $prefix . '%')
            ->select('code', DB::raw("substring_index(substring_index(code,'-',-1),',',-1) as max_no"))->get()
            ->max('max_no');

        if (empty($trans_no)) {
            $code = 1;
        } else {
            $break_no = explode("-", $trans_no);
            $code = end($break_no) + 1;
        }

        $code = $prefix . str_pad($code, 4, '0', STR_PAD_LEFT);
        $data = Tank::all();
        return view('tank.index')->with(compact('data', 'code'));
    }

    public function store(Request $request){
        try {
            DB::beginTransaction();
                $input = [];
                $input['code']      = $request->input('code');
                $input['name']      = $request->input('name');
                $input['weight']    = $request->input('weight');
                $tank = Tank::create($input);

            DB::commit();
            $output = ['msg' => 'success'];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("Filed:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = ['msg' => 'error'];
        }
        return redirect()->back()->with($output);
    }

    public function edit($id){
        $data           = Tank::where('id',$id)->first();
        return view('tank.edit')->with(compact('data'));
    }

    public function update(Request $request){
        try {
            DB::beginTransaction();
                $id = $request->input('id');
                $input = [];
                $input['code']      = $request->input('code');
                $input['name']      = $request->input('name');
                $input['weight']    = $request->input('weight');
                $team = Tank::where('id',$id)->update($input);

            DB::commit();
            $output = ['msg' => 'success'];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("Filed:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = ['msg' => 'error'];
        }
        return redirect()->back()->with($output);
    }
    
    public function destroy($id){
        try {
            DB::beginTransaction();

                Tank::where('id',$id)->delete();
                
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
