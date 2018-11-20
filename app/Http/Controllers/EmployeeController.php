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

                $empRoleID = Role::where('role_name', 'Employee')->first();

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
                $userObject->fk_role_id = $empRoleID->pk_role_id;
        
                $userObject->save();

                $data['status'] = AppConstants::RequestStatusSuccess;
                $data['message'] = 'Employee details savesd successfully';
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
        }
    }
}
