<?php

namespace App\Http\Controllers\App;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BaseController extends Controller
{

    public $today;

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
            $res['data'] = $errorMsg;
        }
        return response()->json($res);
    }

    public function setLog($functionType, $error) : void
    {
        \Log::info($functionType);
        \Log::info($error);
    }
}
