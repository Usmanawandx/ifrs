<?php

namespace App\Http\Controllers;
use DB;
use App\Warranty;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class WarrantyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
            $warranties = Warranty::where('business_id', $business_id)
                         ->select(['id', 'name', 'description', 'duration', 'duration_type']);

            return Datatables::of($warranties)
                ->addColumn(
                    'action',
                    '<button data-href="{{action(\'WarrantyController@edit\', [$id])}}" class="btn btn-xs btn-primary btn-modal btn-edt" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i></button>'
                 )
                 ->removeColumn('id')
                 ->editColumn('duration', function ($row) {
                     return $row->duration . ' ' . __('lang_v1.' .$row->duration_type);
                 })
                 ->rawColumns(['action'])
                 ->make(true);
        }

        return view('warranties.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('warranties.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            DB::beginTransaction();
            $input = $request->only(['name', 'description', 'duration', 'duration_type']);
            $input['business_id'] = $business_id;

            $status = Warranty::create($input);
            DB::commit();
            $output = ['success' => true,
                        'msg' => __("lang_v1.added_success")
                    ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => __("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage())
                        ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Warranty  $warranty
     * @return \Illuminate\Http\Response
     */
    public function show(Warranty $warranty)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Warranty  $warranty
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
            $warranty = Warranty::where('business_id', $business_id)->find($id);

            return view('warranties.edit')
                ->with(compact('warranty'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Warranty  $warranty
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
    
        if (request()->ajax()) {
            try {
                $input = $request->only(['name', 'description', 'duration', 'duration_type']);
                DB::beginTransaction();
                $warranty = Warranty::where('business_id', $business_id)->findOrFail($id);

                $warranty->update($input);
                DB::commit();
                $output = ['success' => true,
                            'msg' => __("lang_v1.updated_success")
                            ];
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }

            return $output;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Warranty  $warranty
     * @return \Illuminate\Http\Response
     */
    public function destroy(Warranty $warranty)
    {
        //
    }
}
