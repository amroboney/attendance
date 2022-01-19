<?php

namespace App\Http\Controllers\App;

use Carbon\Carbon;
use App\Models\Company;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\EmployeeEvent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\App\BaseController;

class AttendanceController extends BaseController
{
    public $employeeID;
    public $company;
    public $checkType;

    public function check(Request $request)
    {
        $this->employeeID = Auth::user()->id;
        $this->company = Company::find(Auth::user()->company_id);

        if ($request->checkType === 'checkIn') {
            return $this->checkIn($request->note, $request->pleaceId);
        }else if ($request->checkType === 'checkOut') {
            return $this->checkOut();
        }else if($request->checkType === 'checkInEvent'){
            return $this->checkInEvent($request->note, $request->pleaceId);
        }else if($request->checkType === 'checkOutEvent'){
            return $this->checkOutEvent($request->pleaceId);
        }
    }


    // check in funciton
    public function checkIn($note, $brachId)
    {
        $yesterday = Carbon::yesterday();
        
        // Yesterday office start time
        /** @var Carbon $yesterday_end_time */
        $yesterday_end_time = clone $this->getOfficeEndTime($yesterday);
        $yesterday_end_time->subDay();

        /** @var Carbon $yesterday_start_time */
        $yesterday_start_time = clone $this->getOfficeStartTime($yesterday);
        $yesterday_start_time->subDay();
        
        // Today and yesterday dates
        $dates = [$this->today->format("Y-m-d"), $yesterday->format("Y-m-d")];

        $today_attendance = Attendance::where('date', $dates[0])
            ->where('employee_id', '=', $this->employeeID)
            ->orderBy('date')
            ->first();

        $yesterday_attendance = Attendance::where('date', $dates[1])
            ->where('employee_id', '=', $this->employeeID)
            ->orderBy('date')
            ->first();


        $working_attendance = null;

        // If less than 6 hours have passed since yesterday's office end time,
        // allow clocking for yesterday

        if ($this->today->diffInHours($yesterday_end_time) <= 6) {
            $working_attendance = $yesterday_attendance;
            $working_day = $yesterday;
        } else {
            $working_attendance = $today_attendance;
            $working_day = $this->today;
        }

        $cur_time = $this->today->format('H:i:s');
        $time = Carbon::now()->timezone($this->company->timezone)->format('h:i A');
        $date_time = Carbon::now()->timezone($this->company->timezone)->format("Y-m-d H:i:s");

        // Check today's attendance
        if ($working_attendance != null) {
            if ($working_attendance->status == "absent") {
                return $this->handleResponse('', 'You have been marked absent for today', 103);
            }
            $working_attendance->clock_in = $cur_time;
            $working_attendance->clock_in_ip_address = $_SERVER['REMOTE_ADDR'];
            $working_attendance->status = 'present';
            $working_attendance->notes = $notes;
            $working_attendance->branch_id = $brachId;
            $working_attendance->office_start_time = $this->company->office_start_time;
            $working_attendance->office_end_time = $this->company->office_end_time;

            if ($this->company->late_mark_after != null) {
                if ($working_attendance->clock_in->diffInMinutes($this->company->getOfficeStartTime($working_day)) <
                    $this->company->late_mark_after * -1) {
                    $working_attendance->is_late = 1;
                } else {
                    $working_attendance->is_late = 0;
                }
            }
            $working_attendance->save();
            $data = ['time' => $time, 'timeDiff' => $this->today->diffForHumans(), 'time_date' => $date_time];

            return $this->handleResponse($data, 'You have successfully clocked in');
        }


        $new_attendance = new Attendance();
        $new_attendance->employee_id = $this->employeeID;;
        $new_attendance->date = $this->today->format("Y-m-d");
        $new_attendance->status = 'present';
        $new_attendance->clock_in = $cur_time;
        $new_attendance->clock_in_ip_address = $_SERVER['REMOTE_ADDR'];
        $new_attendance->notes = $note;
        $new_attendance->branch_id = $brachId;
        $new_attendance->office_start_time = $this->company->office_start_time;
        $new_attendance->office_end_time = $this->company->office_end_time;


        if ($this->company->late_mark_after != null) {
            if ($new_attendance->clock_in->diffInMinutes($this->company->getOfficeStartTime($this->today), false) <
                $this->company->late_mark_after * -1) {
                $new_attendance->is_late = 1;
            } else {
                $new_attendance->is_late = 0;
            }
        }
        $new_attendance->save();
        $data = ['time' => $time, 'timeDiff' => $this->today->diffForHumans(), 'time_date' => $date_time];

        return $this->handleResponse($data, 'check in Successfully');

    }

    // check out for branch
    function checkOut()
    {
        // Creating date objects
        $yesterday = Carbon::yesterday();

        // Yesterday office start time
        /** @var Carbon $yesterday_end_time */
        $yesterday_end_time = clone $this->getOfficeEndTime($yesterday);
        $yesterday_end_time->subDay();

        /** @var Carbon $yesterday_start_time */
        $yesterday_start_time = clone $this->getOfficeStartTime($yesterday);
        $yesterday_start_time->subDay();

        // Today and yesterday dates
        $dates = [$this->today->format("Y-m-d"), $yesterday->format("Y-m-d")];

        $today_attendance = Attendance::where('date', $dates[0])
            ->where('employee_id', '=', $this->employeeID)
            ->orderBy('date')
            ->first();

        $yesterday_attendance = Attendance::where('date', $dates[1])
            ->where('employee_id', '=', $this->employeeID)
            ->orderBy('date')
            ->first();

        $working_attendance = null;

        // If less than 6 hours have passed since yesterday's office end time,
        // allow clocking for yesterday

        if ($this->today->diffInHours($yesterday_end_time) <= 6) {
            $working_attendance = $yesterday_attendance;
        } else {
            $working_attendance = $today_attendance;
        }

        $cur_time = $this->today->format('H:i:s');

        // Check today's attendance
        if ($working_attendance != null) {
            if ($working_attendance->status == "absent") {
                return $this->handleResponse('','You have been marked absent for today', '', 103);                
            }
            if ($working_attendance->clock_in != null) {

                if ($working_attendance->clock_out != null) {
                    return $this->handhandleResponseleError('','Your attendance for today has already been marked', '', 104);
                }
                $working_attendance->clock_out = $cur_time;
                $working_attendance->clock_out_ip_address = $_SERVER['REMOTE_ADDR'];
                $working_attendance->save();

                $clock_out = Carbon::now()->timezone($this->company->timezone);

                $data = ['unset_time' => $clock_out->format("h:i A"), 'unset_time_diff' => $clock_out->diffForHumans(), 'date_time' => $clock_out->format('Y-m-d H:i:s')];
                return $this->handleResponse($data, 'Clock out time was set successfully');
            }

            return $this->handleResponse('','You have to clock in first', '', 105);
        }
        return $this->handleResponse('', 'You have to clock in first', '', 105);

    }

    // event check in
    public function checkInEvent($note, $eventId)
    {
        $cur_time = $this->today->format('H:i:s');
        $time = Carbon::now()->timezone($this->company->timezone)->format('h:i A');
        $date_time = Carbon::now()->timezone($this->company->timezone)->format("Y-m-d H:i:s");

        $employeeEvent = EmployeeEvent::where('employee_id', $this->employeeID)
                        ->where('event_id', $eventId)->first();

        if ($employeeEvent->clock_in != null) {
            return $this->handleResponse('','Your already been check in', 104);
        }else{
            $employeeEvent->date = $this->today->format("Y-m-d");
            $employeeEvent->clock_in = $cur_time;
            $employeeEvent->notes = $note;
            $employeeEvent->update();
        }
            $data = ['time' => $time, 'timeDiff' => $this->today->diffForHumans(), 'time_date' => $date_time];

            return $this->handleResponse($data, 'You have successfully clocked in', 100);
    }

    // event check out
    function checkOutEvent($eventId)
    {
        $cur_time = $this->today->format('H:i:s');
        $time = Carbon::now()->timezone($this->company->timezone)->format('h:i A');
        $date_time = Carbon::now()->timezone($this->company->timezone)->format("Y-m-d H:i:s");


        $employeeEvent = EmployeeEvent::where('employee_id', $this->employeeID)
                        ->where('event_id', $eventId)->first();

        if ($employeeEvent->clock_out != null) {
            return $this->handleResponse('','Your already been check out', 104);
        }else{
            $employeeEvent->date = $this->today->format("Y-m-d");
            $employeeEvent->clock_out = $cur_time;
            $employeeEvent->update();
        }
            $data = ['time' => $time, 'timeDiff' => $this->today->diffForHumans(), 'time_date' => $date_time];

            return $this->handleResponse($data, 'You have successfully clocked Out', 100);

    }

    public function getOfficeEndTime(Carbon $date = null)
    {
        if ($date == null) {
            $date = Carbon::now();
        }

        $dateStr = $date->format("Y-m-d");

        $end = Carbon::createFromFormat("Y-m-d H:i:s", $dateStr . " " . $this->company->office_end_time);
        $start = Carbon::createFromFormat("Y-m-d H:i:s", $dateStr . " " . $this->company->office_start_time);

        if ($end < $start) {
            $end->addDay();
        }

        return $end;
    }

    public function getOfficeStartTime(Carbon $date = null)
    {
        if ($date == null) {
            $date = Carbon::now();
        }

        $dateStr = $date->format("Y-m-d");

        $start = Carbon::createFromFormat("Y-m-d H:i:s", $dateStr . " " . $this->company->office_start_time);

        return $start;
    }


    public function reset()
    {
        try {
            $attendance = Attendance::where('employee_id', Auth::user()->id)
                        ->whereDate('date',$this->today)->firstOrFail();
            $attendance->delete();
            return $this->handleResponse('', 'deleted attendance for today', 100);
        } catch (\Throwable $th) {
            return $this->handleResponse('', 'you dont have attendance for today', 106);
        }
    }


    // get attendance for last 30 days
    public function getAttendance()
    {
        $employeeId = Auth::user()->id;
        $attendance = Attendance::where('created_at',  '>', now()->subDays(30)->endOfDay())
            ->where('employee_id', '=', $employeeId)
            ->orderBy('date')
            ->get();
        return $this->handleResponse($attendance, 'attendance data');
    }


    // check today attendance
    public function checkTodayAttendance($empId)
    {
        return Attendance::where('employee_id', $empId)->whereDate('created_at', $this->today)->exists();  
    }

    public function getTodayAttendance($empId)
    {
        return Attendance::where('employee_id', $empId)->whereDate('created_at', $this->today)->first();
    }


}
