<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Image extends Model
{

    use LogsActivity;
    protected static $logAttributes = ['*'];

    protected $table = 'images';
    public $timestamps = true;


    protected $dates = ['created_at','updated_at'];
    protected $fillable = [
        'custom_key',
        'property_id',
        'path',
        'image_name'
    ];

    public function property(){
        return $this->belongsTo('App\Models\Property','property_id');
    }


}