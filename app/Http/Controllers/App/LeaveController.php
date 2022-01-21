<?php

namespace App\Http\Controllers\App;

use Validator;
use App\Models\Leavetype;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\LeaveApplication;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\App\BaseController as BaseController;

class LeaveController extends BaseController
{
    protected $companyId;
    //getData
    public function getData() 
    {
        $this->companyId = Auth::user()->company_id;
        return Leavetype::where('company_id', $this->companyId)->pluck('leaveType');
        // return $this->leaveTypes;
    }

    // check events Exist or not
    public function checkLeave(){
        $eventCount = count($this->getData());
        if ($eventCount != 0) {return true;} else {return false;};
    }

    // store 
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'leaveFormType' => Rule::in(["single_leaves","date_range"]),
            'startDate'     => 'required|date',
            'endDate'       => 'required_if:leaveFormType,==,"date_range"|nullable|date',
            'leaveType'     => 'required_if:leaveFormType,==,"single_leaves"|nullable|string',
            'reason'        => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return $this->handleError('input validation', $validator->errors(), 107);
        }
        $this->employee =  Auth::user();
        $leaveData = '';
        if ($request->leaveFormType == 'date_range') {
            // save Leave Application Rang
            $leaveData = $this->saveLeaveApplicationRang($request);
        }else {
            // save Leave Application Single
            $leaveData = $this->saveLeaveApplicationSingle($request);
        }
        return $this->handleResponse($leaveData, 'Create Leaves successfully');
    }

    // save Rang
    public function saveLeaveApplicationRang($request)
    {
        $this->days = $this->calcluateDays($request->startDate, $request->endDate);
        $data = [
            'start_date'    => $request->startDate,
            'end_date'      => $request->endDate,
            'days'          => $this->days,
            'leaveType'     => $request->leaveType,
            'reason'        => !empty($request->reason) ? $request->reason : '',
            'company_id'    => $this->employee->company_id,
            'employee_id'   => $this->employee->id,
            'application_status'  =>'pending',
            'applied_on'    => $this->today->format("Y-m-d")
        ];

        return LeaveApplication::create($data); 
    }

    public function saveLeaveApplicationSingle($request)
    {
        $data = [
            'start_date'            => $request->startDate,
            'end_date'              => NULL,
            'days'                  => 1,
            'leaveType'             => $request->leaveType,
            'halfDayType'           => (isset($request->halfleaveType) && $request->halfleaveType == 'yes') ? 'yes' : 'no',
            'reason'                => !empty($request->reason) ? $request->reason : '',
            'company_id'            => $this->employee->company_id,
            'employee_id'           => $this->employee->id,
            'application_status'    => 'pending',
            'applied_on'            => $this->today->format("Y-m-d")
        ];
    
        return LeaveApplication::create($data);
    }

    public function index()
    {
        $this->employee = Auth::user();
        $leaves = LeaveApplication::where('employee_id', $this->employee->id)
                ->where('created_at',  '>', now()->subDays(30)->endOfDay())
                ->get();
        
        return $this->handleResponse($leaves, 'return Leaves successfully');
    }
    
}
