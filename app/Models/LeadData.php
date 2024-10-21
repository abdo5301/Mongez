<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class LeadData extends Model
{

    protected $table = 'lead_data';
    public $timestamps = true;

    use LogsActivity;
    protected static $logAttributes = ['*'];


    protected $dates = ['created_at','updated_at'];
    protected $fillable = [
        'lead_id',
        'name',
        'mobile',
        'email',
        'description',
        'client_id',
        'transfer_by_staff_id',
        'transfer_to_sales_id'
    ];

    public function lead(){
        return $this->belongsTo('App\Models\Lead','lead_id');
    }

    public function client(){
        return $this->belongsTo('App\Models\Client','client_id');
    }

    public function transfer_by_staff(){
        return $this->belongsTo('App\Models\Staff','transfer_by_staff_id');
    }

    public function transfer_to_sales(){
        return $this->belongsTo('App\Models\Staff','transfer_to_sales_id');
    }



}