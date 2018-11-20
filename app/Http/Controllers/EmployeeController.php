<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Carbon\Carbon;

use App\User;
use App\Role;
use App\Designation;
use App\Constants\AppConstants;

class EmployeeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function addEmployee()
    {
        try
        {
            $mstrGender = ['Male', 'Female'];
            $mstrDesigs = Designation::all();
            $mstrRoles = Role::where('pk_role_id', '!=', AppConstants::SUPER_ADMIN_ROLE_ID)->get();

            return view('employee.addemp', compact('mstrDesigs', 'mstrRoles', 'mstrGender'));
        }
        catch(Exception $e)
        {
            Log::debug($e);
        }   
    }

    public function saveEmployee(Request $request) 
    {
        try
        {
            $input = $request->all();

            $valRules = [
                'empname'=>'required',
                'empcode'=>'required',
                'gender'=>'required',
                'designation'=>'required',
                'doj'=>'required|date_format:' . AppConstants::USER_CARBON_FORMAT,
                'mobilenum'=>'required|numeric',
                'emailid'=>'required|email'
            ];
            $valMsgs = [
                'empname.required'=>'Please enter Employee name',
                'empcode.required'=>'Please enter Employee code',
                'emailid.required'=>'Please enter E-mail',
                'emailid.email'=>'The E-mail must be a valid email address',
                'gender.required'=>'Please select Gender',
                'designation.required'=>'Please select Designation',
                'mobilenum.required'=>'Please enter Mobile number',
                'doj.required'=>'Please enter Date of joining',
            ];
            $validator = \Validator::make(
                $input,
                $valRules,
                $valMsgs
            );
            if ($validator->passes())
            {
                $userByEmail = User::getUserByEmailID($input['emailid']);
                if(count($userByEmail) > 0)
                {
                    $data['status'] = AppConstants::RequestStatusFailed;
                    $data['message'] = 'E-mail already exists.';
                    return $data;
                }

                $userByCode = User::getUserByEmpCode($input['empcode']);
                if(count($userByCode) > 0)
                {
                    $data['status'] = AppConstants::RequestStatusFailed;
                    $data['message'] = 'Employee code already exists';
                    return $data;
                }

                $userObject = new User();
                $userObject->name = $input['empname'];
                $userObject->email = $input['emailid'];
                $userObject->password = bcrypt('password');
                $userObject->employee_code = $input['empcode'];
                $userObject->gender = $input['gender'];
                $userObject->date_of_joining = Carbon::createFromFormat(AppConstants::USER_CARBON_FORMAT, $input['doj']);
                $userObject->fk_designation_id = $input['designation'];
                $userObject->mobile_number = $input['mobilenum'];
                $userObject->address = $input['address'];
                $userObject->fk_role_id = AppConstants::EMP_ROLE_ID;
        
                $userObject->save();

                $data['status'] = AppConstants::RequestStatusSuccess;
                $data['message'] = 'Employee details saved successfully';
                return $data;
            }
            else
            {
                $data['status'] = AppConstants::RequestStatusFailed;
                $data['message'] = implode('<br/>', $validator->errors()->all());
                return $data;
            }
        }
        catch(Exception $e)
        {
            Log::debug($e);
            $data['status'] = AppConstants::RequestStatusFailed;
            $data['message'] = 'Some errors found';
            return $data;
        }
    }

    public function listEmployees()
    {
        try
        {
            $mstrRoles = Role::get()
                            ->keyBy('pk_role_id');

            $allEmplyees = User::with('role')
                                ->where('fk_role_id', '!=', AppConstants::SUPER_ADMIN_ROLE_ID)
                                ->orderBy('name')
                                ->paginate(AppConstants::RECORDS_PER_PAGE);

            return view('employee.listemp', compact('allEmplyees', 'mstrRoles'));
        }
        catch(Exception $e)
        {
            Log::debug($e);
        }
    }

    public function changeRole(Request $request)
    {
        try
        {
            $input = $request->all();
            $data = [];

            DB::transaction(function() use($input){
                if( $input['roleId'] == \App\Constants\AppConstants::ADMIN_ROLE_ID )
                {
                    User::where('fk_role_id', \App\Constants\AppConstants::ADMIN_ROLE_ID)
                            ->update(['fk_role_id' => \App\Constants\AppConstants::EMP_ROLE_ID]);
                }
                User::where('pk_user_id',$input['userId'])
                            ->update(['fk_role_id' => $input['roleId']]);
            });

            $data['status'] = AppConstants::RequestStatusSuccess;
            $data['message'] = 'Role of employee changed successfully';
            return $data;
        }
        catch(Exception $e)
        {
            Log::debug($e);

            $data['status'] = AppConstants::RequestStatusFailed;
            $data['message'] = 'Some errors found';
            return $data;
        }
    }
}
