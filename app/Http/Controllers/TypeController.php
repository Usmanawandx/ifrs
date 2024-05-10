<?php

namespace App\Http\Controllers;

use App\child_cat;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
use App\purchasetype;
use App\Type;

class TypeController extends Controller
{

   public function typeIndex()
   {
      $data = Type::where('is_milling', 1)->get();
      return view('Type.index', compact('data'));
   }

   public function store(Request $request)
   {
      try {
         DB::beginTransaction();
         $type = new type;
         $type->name = $request->name;
         $type->purchase_type = $request->purchase_category;
         $type->type = $request->type;
         $type->prefix = $request->prefix;
         $type->is_milling = !empty($request->is_milling) ? $request->is_milling : 0;
         $type->save();

         DB::commit();
         $output = ['success' => true, 'msg' => "success"];
      } catch (\Exception $e) {
         DB::rollBack();
         \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
         $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
      }
      $action = ($type->is_milling == "1") ? 'TypeController@typeIndex' : 'TypeController@ProductIndex';

      return redirect()->action($action)->with('status', $output);
   }


   public function edit($id)
   {
      $saletype = Type::find($id);
      return response()->json($saletype);
   }

   public function update(Request $request, $id)
   {
      try {
         DB::beginTransaction();

         $term = Type::find($id);
         $term->id = $id;
         $term->name = $request->name;
         $term->purchase_type = $request->purchase_category;
         $term->prefix = $request->prefix;
         $term->is_milling = !empty($request->is_milling) ? $request->is_milling : 0;
         $term->save();

         DB::commit();
      } catch (\Exception $e) {
         DB::rollBack();
         \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
         $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
      }
      return response()->json(['success' => true]);
   }

   public function delete($id)
   {
      try {
         DB::beginTransaction();

         $type        = Type::find($id);
         $column_name = ($type->is_milling == "1") ? 'milling_category' : 'product_type';
         $check       = Product::where($column_name, $id)->count();
         if($check == 0){
            $product_category_check    = child_cat::where('sub', $id)->count();
            if($product_category_check == 0){
               $type->delete();
               $output = ['success' => true, 'msg' => "success"];
            }else{
               $output = ['success' => false, 'msg' => "Product Category Exist Of this Product Type"];
            }

         }else{
            $output = ['success' => false, 'msg' => 'Product exist of this Category / Type'];
         }
         
         $action = ($type->is_milling == "1") ? 'TypeController@typeIndex' : 'TypeController@ProductIndex';
         DB::commit();

      } catch (\Exception $e) {

         DB::rollBack();
         \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
         $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
      }
      $action = ($type->is_milling == "1") ? 'TypeController@typeIndex' : 'TypeController@ProductIndex';

      return redirect()->action($action)->with('status', $output);
   }


   public function ProductIndex()
   {
      $data = Type::select('type.*', 'purchase_type.Type as purchase_type')
         ->leftjoin('purchase_type', 'purchase_type.id', '=', 'type.purchase_type')
         ->where('type.type', 'none')->where('is_milling', 0)->get();
      $purchase_category = purchasetype::All();
      return view('Product_type.index', compact('data', 'purchase_category'));
   }


   public function product_type_f($id)
   {
      $type = Type::where('id', $id)->get();
      return response()->json($type);
   }
}
