<?php

namespace App\Http\Controllers\App;

use DateTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BaseController extends Controller
{

    public $today;

    /**
     * @var array
     */
    public $data = [];

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->data[$name];
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function __construct()
    {
        $this->today = Carbon::now();
    }
    
    public function handleResponse($result, $msg, $code = 100)
    {
    	$res = [
            'responseCode'        => $code,
            'responseMessage'     => 'Successfully',
            'responseDescription' => $msg,
            'data'                => $result,
        ];
        return response()->json($res, 200);
    }

    public function handleError($error, $errorMsg = [], $code = 102)
    {
    	$res = [
            'responseCode'          => $code,
            'responseMessage'       => 'Error',
            'responseDescription'   => $error,
        ];
        if(!empty($errorMsg)){
            $res['errors'] = $errorMsg;
        }
        return response()->json($res);
    }

    public function setLog($functionType, $error) : void
    {
        \Log::info($functionType);
        \Log::info($error);
    }

    // calclaute days between 2 date
    public function calcluateDays($startDate, $endDate)
    {
        $datetime1 = new DateTime($startDate);
        $datetime2 = new DateTime($endDate);
        $interval = $datetime1->diff($datetime2);
        return $days = $interval->format('%a');//now do whatever you like with $days
    }
}
