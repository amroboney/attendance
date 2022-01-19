<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        "start_date",
        "end_date",
        "days",
        "leaveType",
        "reason",
        "company_id",
        "employee_id",
        "halfDayType",
        "applied_on", 
        "application_status"
    ];
}