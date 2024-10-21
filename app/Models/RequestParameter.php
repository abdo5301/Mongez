<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class RequestParameter extends Model
{

    use LogsActivity;
    protected static $logAttributes = ['*'];

    protected $table = 'request_parameters';
    public $timestamps = false;

    protected $guarded = ['id'];

    public function request(){
        return $this->belongsTo('App\Models\Request','request_id');
    }

}