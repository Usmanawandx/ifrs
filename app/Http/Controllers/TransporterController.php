<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transporter;
use App\Vehicle;

class TransporterController extends Controller
{
   // Transporter Crud
   public function index()
   {
      $data=Transporter::All();
      return view('Transporter.index',compact('data'));
   }

   public function store(Request $request)
   {
      $transporter=new Transporter;
      $transporter->name=$request->name;
      $transporter->status=$request->status;
      $transporter->save();
      return redirect()->action('TransporterController@index');
   }

   public function edit($id)
   {
      $transporter = Transporter::find($id);
      return response()->json($transporter);
   }
      
   public function update(Request $request, $id)
   {
      $transporter=Transporter::find($id);
      $transporter->id=$id;
      $transporter->name=$request->name;
      $transporter->status=$request->status;
      $transporter->save();
      return response()->json([ 'success' => true ]);
   }

   public function get_transporter($id)
   {
      $get_transporter=Vehicle::where('transporter_id',$id)->get();
      return response()->json($get_transporter);

   }

   public function delete($id)
   {
      $delete = Transporter::find($id);
      $vehicle = Vehicle::where('transporter_id', $id)->get(); 
      $should_delete = true;
       foreach($vehicle as $r) {
        if ($r->transporter_id == $id) {
            $should_delete = false;
            return redirect()->back()->with('message', 'Vehicle Available for This Transporter...');
            break;
        }
        }
        if ($should_delete == true) {
            $delete->delete();
        }
      return redirect()->action('TransporterController@index');
   }

   
}
