<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Client extends Model
{

    protected $table = 'clients';
    public $timestamps = true;

    use LogsActivity;
    protected static $logAttributes = ['*'];

    protected $dates = ['created_at','updated_at'/*,'deleted_at'*/];
    protected $fillable = [
        'type',
        'investor_type',
        'name',
        'company_name',
        'email',
        'phone',
        'mobile1',
        'mobile2',
        'fax',
        'website',
        'address',
        'description',
        'status',
        'created_by_staff_id'
    ];


    public function staff(){
        return $this->belongsTo('App\Models\Staff','created_by_staff_id');
    }


    public function requests(){
        return $this->hasMany('App\Models\Request','client_id');
    }


    public function property(){
        return $this->hasMany('App\Models\Property','client_id');
    }

    public function calls(){
        return $this->hasMany('App\Models\Call','client_id')
            ->select([
                'id',
                'client_id',
                'call_purpose_id',
                'call_status_id',
                'type',
                'description',
                'created_by_staff_id',
                'created_at'
            ])
            ->orderByDesc('id')
            ->with([
                'client',
                'call_purpose',
                'call_status',
                'staff'
            ]);
    }

    public function reminders(){
        return $this->morphMany('App\Models\Reminder','sign')
            ->orderByDesc('date_time')
            ->with([
                'staff'
            ]);
    }

}