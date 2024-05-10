<?php

namespace App\Http\Controllers;

use App\category_list;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\subcategory;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    //
    public function index()
    {
       
        $category=category_list::All();
        return view('categories.index',compact('category'));
    }


    public function store(Request $request)
    {

      $type=new category_list;
      $type->name=$request->name;
      $type->save();


          return redirect()->action('CategoryController@index');


    }


    public function edit($id)
    {
        $saletype = category_list::find($id);

        return response()->json($saletype);
     }
     
     public function update(Request $request, $id)
    {
        
      
    //   $term=new TermsConditions;
       $term=category_list::find($id);
       $term->id=$id;
       $term->name=$request->name;
       $term->save();
       

      return response()->json([ 'success' => true ]);

   }
    public function delete($id)
    {
        try{
       $delete=category_list::find($id);
       $delete->delete();
              
    } catch (\Exception $e) {
    
        $output = ['success' => 0,
                        'msg' => "Transactions available already"
                    ];
        return back()->with('status', $output);
    }
        return redirect()->action('CategoryController@index');
      

    }

    public function subIndex()
    {
        $sub=subcategory::select('subcategory.*','child_category.name as parent')->leftjoin('child_category', 'subcategory.parent_id', '=', 'child_category.id')->get();
        $parent=DB::table('child_category')->get();
        return view('sub_categories.index',compact('sub','parent'));
    }

    public function store_sub(Request $request)
    {

      $subcategory=new subcategory;
      $subcategory->parent_id=request()->parent_id;
      $subcategory->name=$request->name;
      $subcategory->save();

          return redirect()->action('CategoryController@subIndex');


    }


    public function edit_sub($id)
    {
        $saletype = subcategory::find($id);

        return response()->json($saletype);
     }
     
     public function update_sub(Request $request, $id)
    {
        
      
    //   $term=new TermsConditions;
       $term=subcategory::find($id);
       $term->id=$id;
       $term->parent_id=request()->parent_id;
       $term->name=$request->name;
       $term->save();
       

      return response()->json([ 'success' => true ]);

   }
    public function delete_sub($id)
    {
        try{
        // dd("sa");
       $delete=subcategory::find($id);
       $delete->delete();
          
          
    } catch (\Exception $e) {
    
        $output = ['success' => 0,
                        'msg' => "Transactions available already"
                    ];
        return back()->with('status', $output);
    }
      
      return redirect()->action('CategoryController@subIndex');

    }





}
