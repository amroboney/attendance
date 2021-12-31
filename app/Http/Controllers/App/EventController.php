<?php

namespace App\Http\Controllers\App;

use Carbon\Carbon;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\EmployeeEvent;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\App\BaseController as BaseController;

class EventController extends BaseController
{
    public $employeeID;
    //get all active event
    public function index()
    {
        $this->employeeID = Auth::user()->id;
        $today = Carbon::now()->format("Y-m-d");

        $events = EmployeeEvent::with('events')->whereHas('events', function($event) use ($today){
            $event->whereDate('date', '>=', $today);
        })->where('employee_id', $this->employeeID)->get();

        return $this->handleResponse($events, 'get Event successfully');
    }


    public function getEvents()
    {
        $this->employeeID = Auth::user()->id;
        $events = EmployeeEvent::with('events')->whereHas('events', function($event) use ($today){
            $event->whereDate('date', '>=', $today);
        })
        ->where('created_at',  '>', now()->subDays(30)->endOfDay())
        ->where('employee_id', $this->employeeID)->get();

        return $this->handleResponse($attendance, 'attendance data');
    }
}
