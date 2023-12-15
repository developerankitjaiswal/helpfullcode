<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = 'user_logs';
    protected $guarded = array();

    function addToLog($subject, $querySubject, $queryRequest)
    {
        if ($queryRequest != NULL) {
            $queryRequest = json_encode($queryRequest);
        }

        $log = [];
        $log['subject'] = $subject;
        $log['query_subject'] = $querySubject;
        $log['query_request'] = $queryRequest;
        $log['url'] = request()->fullUrl();
        $log['method'] = request()->method();
        $log['ip'] = request()->ip();
        $log['agent'] = request()->header('user-agent');
        $log['user_id'] = auth()->check() ? auth()->user()->id : 0;

        static::create($log);

        return true;
    }
}
