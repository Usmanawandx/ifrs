<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transporter;
use App\Vehicle;
use App\Contact;

class VehicleController extends Controller
{
   // Vehicle Crud
   public function index()
   {

      if (!auth()->user()->can('vehicle.view') ) {
         abort(403, 'Unauthorized action.');
     }

      $data=Vehicle::with('transporter')->get();
      // dd($data);
      $transporters = Contact::where('type','Transporter')->get();
      return view('Vehicle.index',compact('data','transporters'));
   }

   public function store(Request $request)
   {
      $vehicle=new Vehicle;
      $vehicle->vhicle_number=$request->number;
      $vehicle->transporter_id=$request->transporter;
      $vehicle->save();
      return redirect()->action('VehicleController@index');
   }

   public function edit($id)
   {
      if (!auth()->user()->can('transporter.update') ) {
         abort(403, 'Unauthorized action.');
     }
      
      $vehicle = Vehicle::find($id);
      return response()->json($vehicle);
   }
      
   public function update(Request $request, $id)
   {
      $term=Vehicle::find($id);
      $term->id=$id;
      $term->vhicle_number=$request->number;
      $term->transporter_id=$request->transporter;
      $term->save();
      return response()->json([ 'success' => true ]);
   }

   public function delete($id)
   {
      $delete=Vehicle::find($id);
      $delete->delete();
      return redirect()->action('VehicleController@index');
   }

   
}
