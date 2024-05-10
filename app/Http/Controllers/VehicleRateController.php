<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\vehicle_rates;
use App\Contact;
use App\Vehicle;
use App\child_cat;
use App\Transporter;
use DB;

class VehicleRateController extends Controller
{

     public function index()
     {

          
     if (!auth()->user()->can('transporter_rate.view') ) {
          abort(403, 'Unauthorized action.');
     }
          $data = vehicle_rates::leftJoin('contacts', function ($join) {
               $join->on('contacts.id', '=', 'vehicle_rate.vehicle_id');
          })->whereNull('parent_id')->select('*', 'vehicle_rate.id as rate_id')->get();
          $vehicle = Contact::where('type', 'transporter')->get();
          $sub = child_cat::All();
          return view('VehicleRate.index', compact('data', 'vehicle', 'sub'));
     }

     public function create()
     {
          if (!auth()->user()->can('transporter_rate.create') ) {
               abort(403, 'Unauthorized action.');
           }

          $vehicle    = Contact::where('type', 'transporter')->get();
          $sub        = \DB::table('subcategory')->get();
          return view('VehicleRate.create', compact('vehicle', 'sub'));
     }

     public function store(Request $request)
     {
          try {
               DB::beginTransaction();
               $vehicle = new vehicle_rates;
               $vehicle->date          = $request->date;
               $vehicle->vehicle_id    = $request->vehicle;
               $vehicle->save();

               foreach ($request->child_id as $key => $val) {
                    $vehicle_child              = new vehicle_rates;
                    $vehicle_child->parent_id   = $vehicle->id;
                    $vehicle_child->date        = $request->date;
                    $vehicle_child->vehicle_id  = $request->vehicle;
                    $vehicle_child->rate        = $request->vehicle_rate[$key];
                    $vehicle_child->child_id    = $request->child_id[$key];
                    $vehicle_child->save();
               }
               DB::commit();
               $output = ['success' => true, 'msg' => "Added Successfully"];
          } catch (\Exception $e) {
               DB::rollBack();
               \Log::emergency("File:" . $e->getFile() . " Line:" . $e->getLine() . " Message:" . $e->getMessage());
               $output = ['success' => false, 'msg' => $e->getMessage()];
          }
          return redirect()->action('VehicleRateController@index')->with('status', $output);
     }

     public function edit($id)
     {
          if (!auth()->user()->can('transporter_rate.update') ) {
               abort(403, 'Unauthorized action.');
           }
          $vehicle = Contact::where('type', 'transporter')->get();
          $sub = \DB::table('subcategory')->get();
          $vehicle_rates = vehicle_rates::find($id);
          $vehicle_child = vehicle_rates::where('parent_id', $vehicle_rates->id)->get();
          return view('VehicleRate.edit', compact('vehicle_rates', 'vehicle_child', 'vehicle', 'sub'));
     }

     public function show($id)
     {
          $contact = Contact::get();
          $sub = \DB::table('subcategory')->get();
          $vehicle_rates = vehicle_rates::find($id);
          $vehicle_child = vehicle_rates::where('parent_id', $vehicle_rates->id)->get();
          return view('VehicleRate.show', compact('vehicle_rates', 'vehicle_child', 'contact', 'sub'));
     }

     public function update(Request $request, $id)
     {
        

          try {
               DB::beginTransaction();
               $vehicle = vehicle_rates::find($id);
               $vehicle->date   = $request->date;
               $vehicle->vehicle_id  = $request->vehicle;
               $vehicle->save();

               $delete = vehicle_rates::where('parent_id', $id)->delete();

               foreach ($request->child_id as $key => $val) {
                    $vehicle_child   = new vehicle_rates;
                    $vehicle_child->parent_id = $vehicle->id;
                    $vehicle_child->date        = $request->date;
                    $vehicle_child->vehicle_id  = $request->vehicle;
                    $vehicle_child->rate = $request->vehicle_rate[$key];
                    $vehicle_child->child_id = $request->child_id[$key];
                    $vehicle_child->save();
               }
               DB::commit();
               $output = ['success' => true, 'msg' => "Update Successfully"];
          } catch (\Exception $e) {
               DB::rollBack();
               \Log::emergency("File:" . $e->getFile() . " Line:" . $e->getLine() . " Message:" . $e->getMessage());

               $output = ['success' => false, 'msg' => $e->getMessage()];
          }

          return redirect()->action('VehicleRateController@index')->with('status', $output);
     }


     public function delete($id)
     {
          $delete = vehicle_rates::find($id)->delete();
          $delete_child = vehicle_rates::where('parent_id', $id)->delete();
          return redirect()->action('VehicleRateController@index');
     }
}
