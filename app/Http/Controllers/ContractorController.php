<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\contractor_rates;
use App\Contact;
use App\child_cat;
use App\Transaction;
use DB;

class ContractorController extends Controller
{

    public function index()
    {

        if (!auth()->user()->can('contractor_rate.view')) {
            abort(403, 'Unauthorized action.');
        }

        $data = contractor_rates::leftJoin('contacts', function ($join) {
            $join->on('contacts.id', '=', 'contractor_rate.contractor_id');
        })->whereNull('parent_id')->select('*', 'contractor_rate.id as rate_id')->get();
        $contact = Contact::where('type', 'contracter')->get();
        $sub = child_cat::All();
        return view('Contractor.index', compact('data', 'contact', 'sub'));
    }

    public function create()
    {
        $contact    = Contact::where('type', 'contracter')->get();
        $sub        = \DB::table('subcategory')->get();
        return view('Contractor.create', compact('contact', 'sub'));
    }

    public function store(Request $request)
    {

        DB::beginTransaction();

        try {
            $contractor = new contractor_rates;
            $contractor->date   = $request->date;
            $contractor->contractor_id  = $request->contractor;
            $contractor->save();


            foreach ($request->child_id as $key => $val) {
                $contractor_child   = new contractor_rates;
                $contractor_child->parent_id = $contractor->id;
                $contractor_child->date   = $request->date;
                $contractor_child->contractor_id  = $request->contractor;
                $contractor_child->rate = $request->contractor_rate[$key];
                $contractor_child->child_id = $request->child_id[$key];
                $contractor_child->save();
            }
            DB::commit(); // Commit the transaction
            $output = [
                'success' => true,
                'msg' => "Add Successfully"
            ];
        } catch (\Exception $e) {
            DB::rollBack(); // Roll back the transaction in case of an exception

            \Log::emergency("File:" . $e->getFile() . " Line:" . $e->getLine() . " Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => $e->getMessage()
            ];
        }
        return redirect()->action('ContractorController@index')->with('status', $output);
    }

    public function edit($id)
    {
        $contact = Contact::where('type', 'contracter')->get();
        $sub = \DB::table('subcategory')->get();
        $contractor_rates = contractor_rates::find($id);
        $contractor_child = contractor_rates::where('parent_id', $contractor_rates->id)->get();
        return view('Contractor.edit', compact('contractor_rates', 'contractor_child', 'contact', 'sub'));
    }

    public function show($id)
    {
        $contact = Contact::where('type', 'contracter')->get();
        $sub = \DB::table('subcategory')->get();
        $contractor_rates = contractor_rates::find($id);
        $contractor_child = contractor_rates::where('parent_id', $contractor_rates->id)->get();
        return view('Contractor.show', compact('contractor_rates', 'contractor_child', 'contact', 'sub'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction(); // Start a database transaction

        try {
            $contractor = contractor_rates::find($id);
            $contractor->date = $request->date;
            $contractor->contractor_id = $request->contractor;
            $contractor->save();

            $delete = contractor_rates::where('parent_id', $id)->delete();

            foreach ($request->child_id as $key => $val) {
                $contractor_child = new contractor_rates;
                $contractor_child->parent_id = $contractor->id;
                $contractor_child->date   = $request->date;
                $contractor_child->contractor_id  = $request->contractor;
                $contractor_child->rate = $request->contractor_rate[$key];
                $contractor_child->child_id = $request->child_id[$key];
                $contractor_child->save();
            }

            $output = [
                'success' => true,
                'msg' => "Contractor rates updated successfully"
            ];
            DB::commit(); // Commit the transaction

        } catch (\Exception $e) {
            DB::rollBack(); // Roll back the transaction in case of an exception

            \Log::emergency("File:" . $e->getFile() . " Line:" . $e->getLine() . " Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => "Not Update"
            ];
        }
        return redirect()->action('ContractorController@index')->with('status', $output);
    }



    public function delete($id)
    {

        DB::beginTransaction();
        try {
            $business_id = request()->user()->business_id;
            $contractor_rates = contractor_rates::where('id', $id)->first();
            //Check if any transaction related to this contact exists
            $count = Transaction::where('business_id', $business_id)
                ->where('contractor', $contractor_rates->contractor_id)
                ->count();

            if ($count == 0) {

                $delete = contractor_rates::find($id)->delete();
                $delete_child = contractor_rates::where('parent_id', $id)->delete();

                DB::commit();
                $output = [
                    'success' => true,
                    'msg' => __("contact.deleted_success")
                ];
            } else {
                $output = [
                    'success' => false,
                    'msg' => "connot delete because transaction is already exist"
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return redirect()->action('ContractorController@index')->with('status', $output);
    }
}
