<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    protected $fillable = [
        'method',
        'path',
        'ip',
        'request_body',
        'response_body',
        'status_code',
        'duration'
    ];
}
