<?php

namespace App\Http\Controllers;

use App\combine_country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\country;

class CountryController extends Controller
{
    //
public function index()
{
$data=country::All();
return view('Country.index',compact('data'));
}

public function store(Request $request)
{

    $data = new Country;
    $data->type = $request->type;
    $data->name = $request->name;
    $data->save();
    return redirect()->action('CountryController@index');

}

public function combineIndex()
{
    $data=combine_country::with('countrys','citys','states')->get();
    $country=country::where('type','Country')->get()->Pluck('name','id');
    $city=country::where('type','City')->get()->Pluck('name','id');
    $state=country::where('type','State')->get()->Pluck('name','id');

    return view('Combine.index',compact('data','city','state','country'));
}
public function store_combine(Request $request){

    $data = new combine_country;
    $data->country = $request->country;
    $data->state = $request->state;
    $data->city = $request->city;
    $data->save();

    return redirect()->action('CountryController@combineIndex');

}

public function get_data($id)
{
    // dd($id);
    $data=combine_country::where('country',$id)->first();
    // dd($data);

    // $city=combine_country::where('country',$data->country)->

    $datas = combine_country::where('country',$data->country)->leftJoin('country', function ($join) {
        $join->on('combine_country.state', '=', 'country.id');
    })->groupBy('combine_country.state')->get();

    // dd($datas);
    return response()->json($datas);

}

public function get_city($id)
{
    // dd($id);
    
    $data=combine_country::where('state',$id)->first();
    // dd($data);

    // $city=combine_country::where('country',$data->country)->

    $datas = combine_country::where('state',$data->state)->leftJoin('country', function ($join) {
        $join->on('combine_country.city', '=', 'country.id');
    })->groupBy('combine_country.city')->get();

    // dd($datas);
    return response()->json($datas);

}

}
