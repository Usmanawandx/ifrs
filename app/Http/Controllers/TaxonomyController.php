<?php

namespace App\Http\Controllers;

use DB;
use App\Category;
use App\category_list;
use App\child_cat;
use App\subcategory;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TaxonomyController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $category_type = request()->get('type');
        if ($category_type == 'product' && !auth()->user()->can('category.view') && !auth()->user()->can('category.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $category = Category::where('business_id', $business_id)
                ->where('category_type', $category_type)
                ->select(['name', 'short_code', 'description', 'id', 'parent_id']);

            return Datatables::of($category)
                ->addColumn(
                    'action',
                    '
                    <button data-href="{{action(\'TaxonomyController@edit\', [$id])}}?type=' . $category_type . '" class="btn btn-xs btn-primary edit_category_button"><i class="glyphicon glyphicon-edit"></i>  @lang("messages.edit")</button>
                        &nbsp;
                    
                        <button data-href="{{action(\'TaxonomyController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_category_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    '
                )
                ->editColumn('name', function ($row) {
                    if ($row->parent_id != 0) {
                        return '--' . $row->name;
                    } else {
                        return $row->name;
                    }
                })
                ->removeColumn('id')
                ->removeColumn('parent_id')
                ->rawColumns(['action'])
                ->make(true);
        }

        $module_category_data = $this->moduleUtil->getTaxonomyData($category_type);

        return view('taxonomy.index')->with(compact('module_category_data', 'module_category_data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $category_type = request()->get('type');
        if ($category_type == 'product' && !auth()->user()->can('category.view') && !auth()->user()->can('category.create')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');

        $module_category_data = $this->moduleUtil->getTaxonomyData($category_type);

        $categories = Category::where('business_id', $business_id)
            ->where('parent_id', 0)
            ->where('category_type', $category_type)
            ->select(['name', 'short_code', 'id'])
            ->get();

        $parent_categories = [];
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $parent_categories[$category->id] = $category->name;
            }
        }



        $child_cat = child_cat::All()->pluck('name', 'id');

        return view('taxonomy.create')
            ->with(compact('parent_categories', 'child_cat', 'module_category_data', 'category_type'));
    }


    // 

    public function get_child($id)
    {
        // dd($id);
        $find = child_cat::where('sub', $id)->first();
        // $data=Category::where('parent_id',$find->parent_id)->get();

        // dd($find);
        $data = [];
        if ($find) {
            $data = child_cat::with('child')->where('sub', $find->sub)->get();
        }



        // dd($data);

        return response()->json($data);
    }

    // 


    public function get_sub($id)
    {
        // dd($id);
        $find = subcategory::where('parent_id', $id)->first();
        // $data=Category::where('parent_id',$find->parent_id)->get();

        // dd($find->parent_id);

        $data = [];
        if ($find) {
            $data = subcategory::with('category')->where('parent_id', $find->parent_id)->get();
        }

        return response()->json($data);
    }





    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $category_type = request()->input('category_type');
        if ($category_type == 'product' && !auth()->user()->can('category.view') && !auth()->user()->can('category.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['name', 'short_code', 'category_type', 'description']);
            if (!empty($request->input('add_as_sub_cat')) &&  $request->input('add_as_sub_cat') == 1 && !empty($request->input('parent_id'))) {
                $input['parent_id'] = $request->input('parent_id');
                $input['child_id'] = $request->input('child_id');
            } else {
                $input['parent_id'] = 0;
                $input['child_id'] = 0;
            }
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['created_by'] = $request->session()->get('user.id');

            $category = Category::create($input);
            $output = [
                'success' => true,
                'data' => $category,
                'msg' => __("category.added_success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => $e->getMessage()
            ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category_type = request()->get('type');
        if ($category_type == 'product' && !auth()->user()->can('category.view') && !auth()->user()->can('category.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $category = Category::where('business_id', $business_id)->find($id);

            $module_category_data = $this->moduleUtil->getTaxonomyData($category_type);

            $parent_categories = Category::where('business_id', $business_id)
                ->where('parent_id', 0)
                ->where('category_type', $category_type)
                ->where('id', '!=', $id)
                ->pluck('name', 'id');
            $is_parent = false;

            if ($category->parent_id == 0) {
                $is_parent = true;
                $selected_parent = null;
            } else {
                $selected_parent = $category->parent_id;
            }

            $child = child_cat::All();

            return view('taxonomy.edit')
                ->with(compact('category', 'child', 'parent_categories', 'is_parent', 'selected_parent', 'module_category_data'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        if (request()->ajax()) {
            try {
                $input = $request->only(['name', 'description']);
                $business_id = $request->session()->get('user.business_id');

                $category = Category::where('business_id', $business_id)->findOrFail($id);
                $category->name = $input['name'];
                $category->description = $input['description'];
                $category->short_code = $request->input('short_code');

                if (!empty($request->input('add_as_sub_cat')) &&  $request->input('add_as_sub_cat') == 1 && !empty($request->input('parent_id'))) {
                    $category->parent_id = $request->input('parent_id');
                    $category->child_id = $request->input('child_id');
                } else {
                    $category->child_cat = 0;
                }
                $category->save();

                $output = [
                    'success' => true,
                    'msg' => __("category.updated_success")
                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                $category = Category::where('business_id', $business_id)->findOrFail($id);
                $category->delete();

                $output = [
                    'success' => true,
                    'msg' => __("category.deleted_success")
                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }

    public function getCategoriesApi()
    {
        try {
            $api_token = request()->header('API-TOKEN');

            $api_settings = $this->moduleUtil->getApiSettings($api_token);

            $categories = Category::catAndSubCategories($api_settings->business_id);
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->respondWentWrong($e);
        }

        return $this->respond($categories);
    }

    /**
     * get taxonomy index page
     * through ajax
     * @return \Illuminate\Http\Response
     */
    public function getTaxonomyIndexPage(Request $request)
    {
        if (request()->ajax()) {
            $category_type = $request->get('category_type');
            $module_category_data = $this->moduleUtil->getTaxonomyData($category_type);

            return view('taxonomy.ajax_index')
                ->with(compact('module_category_data', 'category_type'));
        }
    }


    public function ChildIndex()
    {
        $data = child_cat::select('child_category.*', 'type.name as parent')
            ->leftjoin('type', 'child_category.sub', '=', 'type.id')->get();
        $sub  = DB::table('type')->where('is_milling', 0)->get();
        return view('child_category.index', compact('data', 'sub'));
    }

    public function cat_store(Request $request)
    {
        try {
            DB::beginTransaction();
            $type = new child_cat();
            $type->name = $request->name;
            $type->sub = $request->sub;
            $type->save();
            DB::commit();
            $output = ['success' => true, 'msg' => "success"];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }
        return redirect()->action('TaxonomyController@ChildIndex')->with('status', $output);
    }

    public function edit_child($id)
    {
        $saletype = child_cat::find($id);
        return response()->json($saletype);
    }

    public function update_child(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $term = child_cat::find($id);
            $term->id = $id;
            $term->name = $request->name;
            $term->sub = $request->sub;
            $term->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
        }
        return response()->json(['success' => true]);
    }



    public function delete($id)
    {
        try {
            DB::beginTransaction();
                $checkSubCat = subcategory::where('parent_id', $id)->count();
                if($checkSubCat == 0){
                    $delete = child_cat::find($id);
                    $delete->delete();
                    $output = ['success' => true, 'msg' => "success"];
                }else{
                    $output = ['success' => false, 'msg' => "SubCategory Exist Of This"];
                }
                
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }
        return redirect()->action('TaxonomyController@ChildIndex')->with('status', $output);
    }


    public function add_category(Request $request)
    {
        // dd($request->category);
        $type = new category_list();
        $type->name = $request->category;
        $type->save();

        $cat = category_list::orderBy('id', 'desc')->get();

        return response()->json($cat);
    }

    public function add_subcategory(Request $request)
    {
        // dd($request);
        $subcategory = new subcategory();
        $subcategory->parent_id = $request->parent_id;
        $subcategory->name = $request->category;
        $subcategory->save();
        // dd($subcategory);

        $sub = subcategory::orderBy('id', 'desc')->get();

        return response()->json($sub);
    }

    public function add_child(Request $request)
    {
        // dd($request);
        $child = new child_cat();
        $child->name = $request->category;
        $child->sub = $request->child_cat;
        $child->save();
        // dd($subcategory);
        $sub = child_cat::orderBy('id', 'desc')->get();

        return response()->json($sub);
    }
}
