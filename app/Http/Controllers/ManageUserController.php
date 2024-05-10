<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\Contact;
use App\Account;
use App\AccountTransaction;
use App\AccountType;
use App\System;
use App\User;
use App\Utils\ModuleUtil;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Activitylog\Models\Activity;

class ManageUserController extends Controller
{
    /**
     * Constructor
     *
     * @param Util $commonUtil
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
        if (!auth()->user()->can('user.view') && !auth()->user()->can('user.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');

            $users = User::where('users.business_id', $business_id)
                        ->user()
                        ->where('is_cmmsn_agnt', 0)
                        ->leftJoin('categories as dep', 'users.essentials_department_id', '=', 'dep.id')
                        ->leftJoin('categories as des', 'users.essentials_designation_id', '=', 'des.id')
                        ->select(['users.id', 'username',
                            DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"),
                             'email', 'allow_login','dep.name as dep','des.name as des']);

            return Datatables::of($users)
                ->editColumn('username', '{{$username}} @if(empty($allow_login)) <span class="label bg-gray">@lang("lang_v1.login_not_allowed")</span>@endif')
                ->addColumn(
                    'role',
                    function ($row) {
                        $role_name = $this->moduleUtil->getUserRoleName($row->id);
                        return $role_name;
                    }
                )
                ->addColumn(
                    'action',
                    '@can("user.update")
                        <a href="{{action(\'ManageUserController@edit\', [$id])}}" class="btn btn-xs btn-primary btn-edt"><i class="glyphicon glyphicon-edit"></i></a>
                        &nbsp;
                    @endcan
                    @can("user.view")
                    <a href="{{action(\'ManageUserController@show\', [$id])}}" class="btn btn-xs btn-info btn-vew"><i class="fa fa-eye"></i></a>
                    &nbsp;
                    @endcan
                    @can("user.delete")
                        <button data-href="{{action(\'ManageUserController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_user_button btn-dlt"><i class="glyphicon glyphicon-trash"></i></button>
                    @endcan'
                )
                ->filterColumn('full_name', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) like ?", ["%{$keyword}%"]);
                })
                ->removeColumn('id')
                ->rawColumns(['action', 'username','dep','des'])
                ->make(true);
        }

        return view('manage_user.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('user.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $roles  = $this->getRolesArray($business_id);
        $username_ext = $this->moduleUtil->getUsernameExtension();
        $locations = BusinessLocation::where('business_id', $business_id)
                                    ->Active()
                                    ->get();
                                    
        // $transaction_account = AccountType::get();
        $transaction_account = DB::table('account_types as a')
                                ->leftJoin('account_types as b', 'a.id', '=', 'b.parent_account_type_id')
                                ->select('a.*')
                                ->whereNull('b.id')
                                ->get();
        
        //Get user form part from modules
        $form_partials = $this->moduleUtil->getModuleData('moduleViewPartials', ['view' => 'manage_user.create']);

        return view('manage_user.create')
                ->with(compact('roles', 'username_ext', 'locations', 'form_partials', 'transaction_account'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('user.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();
            if (!empty($request->input('dob'))) {
                $request['dob'] = $this->moduleUtil->uf_date($request->input('dob'));
            }
            
            $request['cmmsn_percent'] = !empty($request->input('cmmsn_percent')) ? $this->moduleUtil->num_uf($request->input('cmmsn_percent')) : 0;

            $request['max_sales_discount_percent'] = !is_null($request->input('max_sales_discount_percent')) ? $this->moduleUtil->num_uf($request->input('max_sales_discount_percent')) : null;
            
            $user = $this->moduleUtil->createUser($request);
            
            // For user account create code
            foreach($request->input('transaction_account') as $key => $val){
                
                $account_name = DB::table('account_types')->where('id', $val)->first();
                $acc_name = $account_name->name ?? '';
                
                $inputs = [];
                $business_id                = $request->session()->get('user.business_id');
                $user_id                    = $request->session()->get('user.id');
                $name                       = $request->input('surname').' '.$request->input('first_name').' '.$request->input('last_name').' ('. $acc_name .')';
                
                $inputs['name']             = $name;
                
                // For get code 
                $accountType = AccountType::findOrFail($val);
                $accountTypeCode = $accountType->sub_types()->max('code'); 
                $accountCode = $accountType->accounts()->where('is_closed', 0)->whereNull('deleted_at')->max('account_number');
                $newCode = max($accountTypeCode,$accountCode);
                if (!$newCode) {
                    $code = $accountType->code . '0001';
                }else{
                    $code =  ($newCode + 1);
                }

                $inputs['account_number']   = $code;
                $inputs['user_id']          = $user->id;
                $inputs['note']             = NULL;
                $inputs['account_type_id']  = $val;
                $inputs['account_details']  = NULL;
                $inputs['business_id']      = $business_id;
                $inputs['created_by']       = $user_id;
                
                $account = Account::create($inputs);
            }
                
            
            
            DB::commit();
            $output = ['success' => 1,
                        'msg' => __("user.user_added")
                    ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                        // 'msg' => __("messages.something_went_wrong")
                        'msg' => "File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage()
                    ];
        }

        if ($request->input('submit_type') == 'submit') {
            return redirect()->action(
                'ManageUserController@index'
            );
        } elseif ($request->input('submit_type') == 'save_n_add_another') {
            return redirect()->action(
                'ManageUserController@create'
            )->with('status', $output);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('user.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $user = User::where('business_id', $business_id)
                    ->with(['contactAccess'])
                    ->find($id);

        //Get user view part from modules
        $view_partials = $this->moduleUtil->getModuleData('moduleViewPartials', ['view' => 'manage_user.show', 'user' => $user]);

        $users = User::forDropdown($business_id, false);

        $activities = Activity::forSubject($user)
           ->with(['causer', 'subject'])
           ->latest()
           ->get();

        return view('manage_user.show')->with(compact('user', 'view_partials', 'users', 'activities'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('user.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $user = User::where('business_id', $business_id)
                    ->with(['contactAccess'])
                    ->findOrFail($id);

        $roles = $this->getRolesArray($business_id);

        $contact_access = $user->contactAccess->pluck('name', 'id')->toArray();

        if ($user->status == 'active') {
            $is_checked_checkbox = true;
        } else {
            $is_checked_checkbox = false;
        }

        $locations = BusinessLocation::where('business_id', $business_id)
                                    ->get();

        $permitted_locations = $user->permitted_locations();
        $username_ext = $this->moduleUtil->getUsernameExtension();

        //Get user form part from modules
        $form_partials = $this->moduleUtil->getModuleData('moduleViewPartials', ['view' => 'manage_user.edit', 'user' => $user]);
        // $transaction_account = AccountType::All();
        $transaction_account = DB::table('account_types as a')
                                ->leftJoin('account_types as b', 'a.id', '=', 'b.parent_account_type_id')
                                ->select('a.*')
                                ->whereNull('b.id')
                                ->get();
        $tr_accout_edit = Account::where('contact_id', $id)->get();

        return view('manage_user.edit')
                ->with(compact('tr_accout_edit','roles', 'user', 'contact_access', 'is_checked_checkbox', 'locations', 'permitted_locations', 'form_partials', 'username_ext', 'transaction_account'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('user.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $user_data = $request->only(['surname', 'first_name', 'last_name', 'email', 'selected_contacts', 'marital_status',
                'blood_group', 'contact_number', 'fb_link', 'twitter_link', 'social_media_1',
                'social_media_2', 'permanent_address', 'current_address',
                'guardian_name', 'custom_field_1', 'custom_field_2',
                'custom_field_3', 'custom_field_4', 'id_proof_name', 'id_proof_number', 'cmmsn_percent', 'gender', 'max_sales_discount_percent', 'family_number', 'alt_number']);
                
                
            // For accounts ids store
            $user_data['account_id']    = $request->input('transaction_account');
            if(count($request->input('transaction_account')) > 0){  
                $user_data['account_id'] = implode(', ', $request->input('transaction_account'));   
            }
                
                

            $user_data['status'] = !empty($request->input('is_active')) ? 'active' : 'inactive';
            $business_id = request()->session()->get('user.business_id');

            if (!isset($user_data['selected_contacts'])) {
                $user_data['selected_contacts'] = 0;
            }

            if (empty($request->input('allow_login'))) {
                $user_data['username'] = null;
                $user_data['password'] = null;
                $user_data['allow_login'] = 0;
            } else {
                $user_data['allow_login'] = 1;
            }

            if (!empty($request->input('password'))) {
                $user_data['password'] = $user_data['allow_login'] == 1 ? Hash::make($request->input('password')) : null;
            }

            //Sales commission percentage
            $user_data['cmmsn_percent'] = !empty($user_data['cmmsn_percent']) ? $this->moduleUtil->num_uf($user_data['cmmsn_percent']) : 0;

            $user_data['max_sales_discount_percent'] = !is_null($user_data['max_sales_discount_percent']) ? $this->moduleUtil->num_uf($user_data['max_sales_discount_percent']) : null;

            if (!empty($request->input('dob'))) {
                $user_data['dob'] = $this->moduleUtil->uf_date($request->input('dob'));
            }

            if (!empty($request->input('bank_details'))) {
                $user_data['bank_details'] = json_encode($request->input('bank_details'));
            }

            DB::beginTransaction();

            if ($user_data['allow_login'] && $request->has('username')) {
                $user_data['username'] = $request->input('username');
                $ref_count = $this->moduleUtil->setAndGetReferenceCount('username');
                if (blank($user_data['username'])) {
                    $user_data['username'] = $this->moduleUtil->generateReferenceNumber('username', $ref_count);
                }

                $username_ext = $this->moduleUtil->getUsernameExtension();
                if (!empty($username_ext)) {
                    $user_data['username'] .= $username_ext;
                }
            }

            $user = User::where('business_id', $business_id)
                          ->findOrFail($id);

            $user->update($user_data);
            
            // For user account create code 
            foreach($request->input('transaction_account') as $key => $val){
                $account_name = DB::table('account_types')->where('id', $val)->first();
                $acc_name = $account_name->name ?? '';
            
                $inputs = [];
                $business_id                = $request->session()->get('user.business_id');
                $user_id                    = $request->session()->get('user.id');
                $name                       = $request->input('surname').' '.$request->input('first_name').' '.$request->input('last_name').' ('. $acc_name .')';
                $inputs['name']             = $name;
                $inputs['user_id']          = $user->id;
                $inputs['note']             = NULL;
                $inputs['account_type_id']  = $val;
                $inputs['account_details']  = NULL;
                $inputs['business_id']      = $business_id;
                $inputs['created_by']       = $user_id;

                $account = Account::where('user_id',$id)->where('account_type_id',$val)->update($inputs);
                if(!$account){
                    // For code/account number create of new account
                    $accountType = AccountType::findOrFail($val);
                    $accountTypeCode = $accountType->sub_types()->max('code'); 
                    $accountCode = $accountType->accounts()->where('is_closed', 0)->whereNull('deleted_at')->max('account_number');
                    $newCode = max($accountTypeCode,$accountCode);
                    if (!$newCode) {
                        $code = $accountType->code . '0001';
                    }else{
                        $code =  ($newCode + 1);
                    }
                    $inputs['account_number']   = $code;
                    Account::create($inputs);
                }
            }
            
            
            
            $role_id = $request->input('role');
            $user_role = $user->roles->first();
            $previous_role = !empty($user_role->id) ? $user_role->id : 0;
            if ($previous_role != $role_id) {
                $is_admin = $this->moduleUtil->is_admin($user);
                $all_admins = $this->getAdmins();
                //If only one admin then can not change role
                if ($is_admin && count($all_admins) <= 1) {
                    throw new \Exception(__('lang_v1.cannot_change_role'));
                }
                if (!empty($previous_role)) {
                    $user->removeRole($user_role->name);
                }
                
                $role = Role::findOrFail($role_id);
                $user->assignRole($role->name);
            }

            //Grant Location permissions
            $this->moduleUtil->giveLocationPermissions($user, $request);

            //Assign selected contacts
            if ($user_data['selected_contacts'] == 1) {
                $contact_ids = $request->get('selected_contact_ids');
            } else {
                $contact_ids = [];
            }
            $user->contactAccess()->sync($contact_ids);

            //Update module fields for user
            $this->moduleUtil->getModuleData('afterModelSaved', ['event' => 'user_saved', 'model_instance' => $user]);

            $this->moduleUtil->activityLog($user, 'edited', null, ['name' => $user->user_full_name]);

            $output = ['success' => 1,
                        'msg' => __("user.user_update_success")
                    ];

            DB::commit();

        } catch (\Exception $e) {

            DB::rollBack();

            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => $e->getMessage()
                        ];
        }

        return redirect('users')->with('status', $output);
    }

    public function checkUserTransactionAccount(Request $request)
    {
        $accountIds          = $request->input('accountIds');
        $user_id             = $request->input('user_id');
        $transaction_account = $request->input('transaction_account');
        
        $missingAccounts = array_diff($accountIds, $transaction_account);
        if(empty($missingAccounts)){
            return response()->json(['success' => 'true', 'msg' => '']);
        }else {
            $accounts   = Account::where('user_id', $user_id)->whereIn('account_type_id', $missingAccounts)->get();
            $accountIds = $accounts->pluck('id');
            $check      = AccountTransaction::whereIn('account_id', $accountIds)->count();
            if ($check === 0) {
                return response()->json(['success' => 'true', 'msg' => '']);
            } else {
                $accountName = Account::whereIn('id', $accountIds)->first();
                return response()->json([
                    'success' => 'false',
                    'msg' => ($accountName ? $accountName->name : '') . ' account has transactions',
                ]);
            }
        }
    }
    

    private function getAdmins()
    {
        $business_id = request()->session()->get('user.business_id');
        $admins = User::role('Admin#' . $business_id)->get();

        return $admins;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('user.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');
                
                $user = User::where('business_id', $business_id)
                    ->findOrFail($id);

                $this->moduleUtil->activityLog($user, 'deleted', null, ['name' => $user->user_full_name, 'id' => $user->id]);

                $user->delete();
                $output = ['success' => true,
                                'msg' => __("user.user_delete_success")
                                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }

            return $output;
        }
    }

    /**
     * Retrives roles array (Hides admin role from non admin users)
     *
     * @param  int  $business_id
     * @return array $roles
     */
    private function getRolesArray($business_id)
    {
        $roles_array = Role::where('business_id', $business_id)->get()->pluck('name', 'id');
        $roles = [];

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        foreach ($roles_array as $key => $value) {
            if (!$is_admin && $value == 'Admin#' . $business_id) {
                continue;
            }
            $roles[$key] = str_replace('#' . $business_id, '', $value);
        }
        return $roles;
    }
}
