<?php

namespace App\Http\Controllers;

use App\store;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StoreController extends Controller
{
    //
   public function index()
   {
    $data=store::All();
    return view('Store.index')->with(compact('data'));    
   }

   public function store(Request $request)
   {

    $store=new store;
    $store->name=$request->name;
    $store->save();

    return redirect()->action('StoreController@index');

   }

   public function store_delete($id)
   {
      $delete=store::find($id);
      $delete->delete();
      return redirect()->action('StoreController@index');

   }


}
