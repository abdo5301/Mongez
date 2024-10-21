<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class PropertyParameter extends Model
{

    use LogsActivity;
    protected static $logAttributes = ['*'];


    protected $table = 'property_parameters';
    public $timestamps = false;

    protected $guarded = ['id'];

    public function property(){
        return $this->belongsTo('App\Models\Property','property_id');
    }

}