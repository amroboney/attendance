<?php

namespace App\Models;


use App\Observers\CompanyObserver;
use Carbon\Carbon;
// use Laravel\Cashier\Billable;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{

    // use Billable;

    protected $fillable = [
        'company_name',
        'contact',
        'address',
        'name',
        'email',
        'country',
        'timezone',
        'logo',
        'locale',
        'billing_address',
        'currency',
        'currency_symbol',
        'award_notification',
        'leave_notification',
        'payroll_notification',
        'attendance_notification',
        'notice_notification',
        'expense_notification',
        'employee_add',
        'front_theme',
        'license_expired',
    ];

    protected $dates = ['deleted_at', 'last_login', 'trial_ends_at', 'subscription_ends_at', 'licence_expire_on'];


    public function branch()
    {
        return $this->hasMany('App\Models\Branch', 'company_id', 'id');
    }

    public function users()
    {
        return $this->hasMany('App\Models\Admin', 'company_id', 'id');
    }

    
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function subscriptionPlan()
    {
        return $this->hasOne('App\Models\Plan', 'id', 'subscription_plan_id');
    }

    public function getLangName()
    {
        // belongs('OtherClass','thisclasskey','otherclasskey')
        return $this->belongsTo('App\Models\Language', 'locale', 'locale');
    }

    public function lastLoginAdmin()
    {
        return Admin::where('company_id', $this->id)->orderBy('last_login', 'desc')->first();
    }

    public function getTimezoneAttribute($value)
    {
        return explode("=", $value)[0];
    }

    public function getTimezoneIndexAttribute()
    {
        return explode("=", $this->attributes["timezone"])[1];
    }

    public function getOfficeEndTime(Carbon $date = null)
    {
        if ($date == null) {
            $date = Carbon::now();
        }

        $dateStr = $date->format("Y-m-d");

        $end = Carbon::createFromFormat("Y-m-d H:i:s", $dateStr . " " . $this->attributes["office_end_time"]);
        $start = Carbon::createFromFormat("Y-m-d H:i:s", $dateStr . " " . $this->attributes["office_start_time"]);

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

        $start = Carbon::createFromFormat("Y-m-d H:i:s", $dateStr . " " . $this->attributes["office_start_time"]);

        return $start;
    }

    public static function dateOf11Employee($company_id)
    {
        $el = Employee::where('company_id', $company_id)->skip(10)->take(1)->first();
        return isset($el->created_at) ? $el->created_at : '-';

    }



    public function departments()
    {
        return $this->hasMany(Department::class);
    }

}
