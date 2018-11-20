<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Carbon\Carbon;

use App\User;
use App\Leave;
use App\Constants\AppConstants;

class LeaveController extends Controller
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

    public function getLeaves(Request $request)
    {
        try
        {
            $input = $request->all();

            $leaveQry = Leave::orderBy('fk_user_id')
                            ->orderBy('from_date')
                            ->orderBy('to_date');

            $showAll = false;
            if(isset($input['showall']) === false) 
            {
                $leaveQry->where('fk_user_id', auth()->user()->pk_user_id);
                $leaveQry->with(['backupEmp']);
            }
            else
            {
                $showAll = true;
                $leaveQry->with(['appliedEmp', 'backupEmp']);
            }

            $curUserLeaves = $leaveQry->paginate(AppConstants::RECORDS_PER_PAGE);

            $leaveHtml = view('leaves.leavelist', compact('curUserLeaves', 'showAll'))->render();
            $data['status'] = AppConstants::RequestStatusSuccess;
            $data['data'] = $leaveHtml;
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

    public function showLeaveForm(Request $request)
    {
        try
        {
            $otherEmpls = User::where('pk_user_id', '!=', auth()->user()->pk_user_id)
                    ->where('fk_role_id', '!=', AppConstants::SUPER_ADMIN_ROLE_ID)
                    ->get();

            return view('leaves.applyleave', compact('otherEmpls', 'curUserLeaves'));
        }
        catch(Exception $e)
        {
            Log::debug($e);
        }
    }

    public function saveLeave(Request $request)
    {
        try
        {
            $input = $request->all();

            $valRules = [
                'fromdate'=>'required|date_format:' . AppConstants::USER_CARBON_FORMAT,
                'todate'=>'required|date_format:' . AppConstants::USER_CARBON_FORMAT,
                'reason'=>'required',
                'otheremp'=>'required'
            ];
            $valMsgs = [
                'fromdate.required'=>'Please enter From date',
                'todate.required'=>'Please enter To date',
                'reason.required'=>'Please enter reason',
                'otheremp.required'=>'Please select Backup Employee'
            ];
            $validator = \Validator::make(
                $input,
                $valRules,
                $valMsgs
            );
            if ($validator->passes())
            {
                $lvObject = new Leave();
                $lvObject->from_date = Carbon::createFromFormat(AppConstants::USER_CARBON_FORMAT, $input['fromdate']);
                $lvObject->to_date = Carbon::createFromFormat(AppConstants::USER_CARBON_FORMAT, $input['todate']);
                $lvObject->fk_backup_user_id = $input['otheremp'];
                $lvObject->reason = $input['reason'];
                $lvObject->fk_user_id = auth()->user()->pk_user_id;
        
                $lvObject->save();

                $data['status'] = AppConstants::RequestStatusSuccess;
                $data['message'] = 'Leave details saved successfully';
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
}
