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
    /*
    |--------------------------------------------------------------------------
    | Employee Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles request for 
    | add new employee, save new employee
    | list all employees and change role of employee 
    |
    */

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
     * Show the view for add new employee
     *
     */
    public function addEmployee()
    {
        try
        {
            // build array for gender
            $mstrGender = ['Male', 'Female'];

            // get all designations
            $mstrDesigs = Designation::all();

            return view('employee.addemp', compact('mstrDesigs', 'mstrGender'));
        }
        catch(Exception $e)
        {
            Log::debug($e);
        }   
    }

    /**
     * Handles save employee ajax request
     *
     */
    public function saveEmployee(Request $request) 
    {
        try
        {
            // get all request data as array
            $input = $request->all();

            // perform validation on input data
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
                // check the emailid already exists
                $userByEmail = User::getUserByEmailID($input['emailid']);
                if(count($userByEmail) > 0)
                {
                    $data['status'] = AppConstants::RequestStatusFailed;
                    $data['message'] = 'E-mail already exists.';
                    return $data;
                }

                // check the employee code already exists
                $userByCode = User::getUserByEmpCode($input['empcode']);
                if(count($userByCode) > 0)
                {
                    $data['status'] = AppConstants::RequestStatusFailed;
                    $data['message'] = 'Employee code already exists';
                    return $data;
                }

                // create user object and save the details
                $userObject = new User();
                $userObject->name = $input['empname'];
                $userObject->email = $input['emailid'];
                // default password - 'password' and encrypt
                $userObject->password = bcrypt('password');
                $userObject->employee_code = $input['empcode'];
                $userObject->gender = $input['gender'];
                // parse date from dmy to ymd format
                $userObject->date_of_joining = Carbon::createFromFormat(AppConstants::USER_CARBON_FORMAT, $input['doj']);
                $userObject->fk_designation_id = $input['designation'];
                $userObject->mobile_number = $input['mobilenum'];
                $userObject->address = $input['address'];
                // default role of employee - Employee
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

    /**
     * List all employees excluding super admin
     *
     */
    public function listEmployees()
    {
        try
        {
            // get all roles
            $mstrRoles = Role::get()
                            ->keyBy('pk_role_id');

            // get all employees except super admin
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

    /**
     * Handles change role of employee
     *
     */
    public function changeRole(Request $request)
    {
        try
        {
            $input = $request->all();
            $data = [];

            User::where('pk_user_id',$input['userId'])
                ->update(['fk_role_id' => $input['roleId']]);

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
