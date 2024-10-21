<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Lead extends Model
{

    protected $table = 'leads';
    public $timestamps = true;

    use LogsActivity;
    protected static $logAttributes = ['*'];


    protected $dates = ['created_at','updated_at'];
    protected $fillable = [
        'name',
        'file',
        'columns_data',
        'created_by_staff_id'
    ];

    public function staff(){
        return $this->belongsTo('App\Models\Staff','created_by_staff_id');
    }

    public function data(){
        return $this->hasMany('App\Models\LeadData','lead_id');
    }

}