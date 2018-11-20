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
    /*
    |--------------------------------------------------------------------------
    | Leave Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles request for 
    | add leaves by employees and list leaves
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
     * List all leaves for admin user
     * List self applied leaves for employees
     *
     */
    public function getLeaves(Request $request)
    {
        try
        {
            $input = $request->all();

            // set order by clause
            $leaveQry = Leave::orderBy('fk_user_id')
                            ->orderBy('from_date')
                            ->orderBy('to_date');

            $showAll = false;
            // when admin user accesses the page, the input array will contain a showall parameter
            // in such case include applied employees name
            if(isset($input['showall']) === false) 
            {
                $leaveQry->where('fk_user_id', auth()->user()->pk_user_id);
                // join for fetching the backup employee id
                $leaveQry->with(['backupEmp']);
            }
            else
            {
                $showAll = true;
                // join for fetching the backup employee id and applied user id
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

    /**
     * Handles request for show leave form
     *
     */
    public function showLeaveForm(Request $request)
    {
        try
        {
            // fetch all users except the current user and admin user 
            // used to populate backup employee list
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

    /**
     * Handles ajax request for save leave 
     *
     */
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
                // start -- overlap validation for leave dates
                $dateFromObj = Carbon::createFromFormat(AppConstants::USER_CARBON_FORMAT, $input['fromdate']);
                $dateToObj = Carbon::createFromFormat(AppConstants::USER_CARBON_FORMAT, $input['todate']);

                $dupCountObj = Leave::where('fk_user_id', auth()->user()->pk_user_id);

                $dtfrmDBStr = $dateFromObj->format(AppConstants::DB_DATE_FORMAT);
                $dttoDBStr = $dateToObj->format(AppConstants::DB_DATE_FORMAT);
                $dupCountObj->where(function ($query) use($dtfrmDBStr, $dttoDBStr) {
                    $query->whereBetween('from_date', [$dtfrmDBStr, $dttoDBStr]);
                    $query->where(function ($querySub) use($dtfrmDBStr, $dttoDBStr) {
                        $querySub->whereBetween('to_date', [$dtfrmDBStr, $dttoDBStr]);
                    });
                });
                
                if ($dupCountObj->count() > 0)
                {
                    $data['status'] = AppConstants::RequestStatusFailed;
                    $data['message'] = 'Overlapping in leave dates found';
                    return $data;
                }
                // end -- overlap validation for leave dates
                
                $lvObject = new Leave();
                $lvObject->from_date = $dateFromObj;
                $lvObject->to_date = $dateToObj;
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
