<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Parameter extends Model
{


    use LogsActivity;
    protected static $logAttributes = ['*'];

    protected $table = 'parameters';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'property_type_id',
        'column_name',
        'name_ar',
        'name_en',
        'type',
        'options',
        'default_value',
        'required',
        'show_in_request',
        'between_request',
        'multi_request',
        'position',
        'created_by_staff_id'
    ];


    public function getOptionsAttribute($value)
    {
        return @unserialize($value);
    }

    public function setOptionsAttribute($value)
    {
        $this->attributes['options'] = @serialize($value);
    }

    public function staff(){
        return $this->belongsTo('App\Models\Staff','created_by_staff_id');
    }

    public function property_type(){
        return $this->belongsTo('App\Models\PropertyType','property_type_id');
    }

}