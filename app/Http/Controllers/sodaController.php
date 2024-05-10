<?php

namespace App\Http\Controllers;

use App\Contact;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\miling_details;
use App\Product;
use App\soda;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Resources\Json\Resource;

class sodaController extends Controller
{
    

    public function index()
    {

    $soda=soda::where('type','Booking')->get();
    return view('soda.index')->with(compact('soda'));
        
    }

    public function soda_report()
    {

        $soda = soda::where('type', 'Dispatch')
                     ->select('date','gate_pass_no','boking_rate','booking_qnty','dispatch_qty',DB::raw('booking_qnty -  dispatch_qty as total'))
                     ->groupBy('customer_id','gate_pass_no')
                     ->get();
                    //  dd($soda);

        // $soda=soda::where('type','Dispatch')->get();
        // $soda=soda::where('type','Dispatch')->get();
        return view('soda.sodaReport')->with(compact('soda'));   
    }

    public function create()
    {
        $customer=Contact::where('type','customer')->where('customer_group_id','2')->get();
        $product=Product::All();
        $t_no = soda::where('type','Booking')->select("id")
        ->orderBy("id",'desc')->take(1)->count();
        // dd($t_no);
        
        if($t_no== 0){
          $soda_id=1; 
        }else{
              $soda_id = $t_no;
        }
        return view('soda.create',compact('product','soda_id','customer'));
    }


    public function millingdetail_index()
    {
        $milling_details=miling_details::All();
        return view('milling_details.index')->with(compact('milling_details'));
    }


    public function millingdetail_create()
    {
        $product=Product::All();
        $contact=Contact::All();
        $t_no = miling_details::select("id")
        ->orderBy("id",'desc')->take(1)->count();
        if($t_no== 0){
          $md_id=1; 
        }else{
              $md_id = $t_no;
        }
        return view('milling_details.create',compact('product','md_id','contact'));
    }

    public function millingdetail_store(Request $request)
    {
        $miling_details=new miling_details;
        $miling_details->party_name=$request->party_name;
        $miling_details->miling_rate=$request->miling_rate;
        $miling_details->oil_rate=$request->oil_rate;
        $miling_details->description=$request->description;
        $miling_details->product_id=$request->product_id;
        $miling_details->gross_weight=$request->gross_weight;
        $miling_details->empty_weight=$request->empty_weight;
        $miling_details->net_weight=$request->net_weight;
        $miling_details->empty_rate=$request->empty_rate;
        $miling_details->unit_price=$request->unit_price;
        $miling_details->qty=$request->qty;
        $miling_details->amount=$request->amount;
        $miling_details->save();
        return redirect()->action('sodaController@millingdetail_index')->with('message', 'Add Successfull');
    }

    public function store(Request $request)
    {
        // dd($request->date);

        $soda=new soda;
        $soda->ref_no=$request->ref_no;
        $soda->date=$request->date;
        $soda->type="Booking";
        $soda->gate_pass_no=$request->gate_pass_no;
        $soda->boking_rate=$request->boking_rate;
        $soda->customer_id=$request->customer_id;
        $soda->product_id=$request->product_id;
        $soda->booking_qnty=$request->booking_qnty;
        $soda->dispatch_qty=$request->dispatch_qty;
        $soda->balance_qty=$request->balance_qty;
        $soda->save();
        return redirect()->action('sodaController@index')->with('message', 'Add Successfull');

    }


    public function get_booking($id)
    {

        // dd($id);
    $soda=soda::where('id',$id)->first();
    // return $soda;
    return response()->json($soda);



    }

    public function get_booking_id($id)
    {
        // dd($id);
    $soda=soda::where('customer_id',$id)->where('type','Booking')->get();
    return response()->json($soda);
    }



    

    public function index_dispatch()
    {
    $soda=soda::where('type','Dispatch')->get();
    return view('soda.index_dispatch')->with(compact('soda'));
    }

    public function create_dispatch()
    {
        $customer=Contact::where('type','customer')->where('customer_group_id','2')->get();
        $product=Product::All();
        $t_no = soda::where('type','Dispatch')
        ->select("id")
        ->orderBy("id",'desc')->take(1)->count();

        $booking_id=soda::where('type','Booking')->get();
        // dd($t_no);
        
        if($t_no== 0){
          $soda_id=1; 
        }else{
              $soda_id = $t_no;
        }
        return view('soda.create_dispatch',compact('product','soda_id','customer','booking_id'));
    }

    public function store_dispatch(Request $request)
    {
        // dd($request->date);

        $soda=new soda;
        $soda->ref_no=$request->ref_no;
        $soda->date=$request->date;
        $soda->type="Dispatch";
        $soda->gate_pass_no=$request->gate_pass_no;
        $soda->boking_rate=$request->boking_rate;
        $soda->product_id=$request->product_id;
        $soda->customer_id=$request->customer_id;
        $soda->booking_id=$request->booking_id;
        $soda->booking_qnty=$request->booking_qnty;
        $soda->dispatch_qty=$request->dispatch_qty;
        $soda->balance_qty=$request->balance_qty;
        $soda->save();

        return redirect()->action('sodaController@index_dispatch')->with('message', 'Add Successfull');

    }



}
