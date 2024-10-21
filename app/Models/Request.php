<?php

namespace App\Models;

use App\Libs\AreasData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Request extends Model
{

    use LogsActivity;
    protected static $logAttributes = ['*'];

    protected $table = 'requests';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['created_at','updated_at','sharing_until','deleted_at'];
    protected $fillable = [
        'property_type_id',
        'purpose_id',
        'data_source_id',
        'client_id',
        'area_ids',
        'request_status_id',
        'name',
        'description',
        'payment_type',
        'deposit_from',
        'deposit_to',
        'price_from',
        'price_to',
        'currency',
        'property_model_id',
        'space_from',
        'space_to',
        'space_type',
        'sales_id',
        'created_by_staff_id',
        'sharing_slug',
        'sharing_until',
        'sharing_views',
        'sharing_staff_id'
    ];


    public function property_type(){
        return $this->belongsTo('App\Models\PropertyType','property_type_id');
    }

    public function purpose(){
        return $this->belongsTo('App\Models\Purpose','purpose_id');
    }

    public function client(){
        return $this->belongsTo('App\Models\Client','client_id');
    }

    public function request_status(){
        return $this->belongsTo('App\Models\RequestStatus','request_status_id');
    }

    public function data_source(){
        return $this->belongsTo('App\Models\DataSource','data_source_id');
    }

    public function sales(){
        return $this->belongsTo('App\Models\Staff','sales_id');
    }



    public function created_by_staff(){
        return $this->belongsTo('App\Models\Staff','created_by_staff_id');
    }


    public function property(){

        $property = Property::join('property_parameters','property_parameters.property_id','=','properties.id')
            ->where([
                ['properties.property_type_id',$this->property_type_id],
                ['properties.purpose_id',$this->purpose_id]
            ]);


        // Area
        $areas = explode(',',$this->area_ids);
        $searchAreas = [];
        foreach ($areas as $key => $value){
            foreach (AreasData::getAreasDown($value) as $newAreaID){
                $searchAreas[] = $newAreaID;
            }
        }

        $property->whereIn('properties.area_id',$searchAreas)
                 ->whereIn('properties.property_status_id',explode(',',setting('available_property_status')));


        // Payment Type
        if($this->payment_type == 'cash'){
            $property->whereIn('properties.payment_type',['cash','cash_installment']);
        }elseif($this->payment_type == 'installment'){
            $property->whereIn('properties.payment_type',['installment','cash_installment']);
        }

        // Deposit
        if(in_array($this->payment_type,['installment','cash_installment'])){
            $property->whereRaw('(properties.deposit BETWEEN ? AND ?)',[$this->deposit_from,$this->deposit_to]);
        }

        $property->whereRaw('(properties.price BETWEEN ? AND ?)',[$this->price_from,$this->price_to]);
        $property->where('properties.currency',$this->currency);

        $property->whereRaw('(properties.space BETWEEN ? AND ?)',[$this->space_from,$this->space_to]);
        $property->where('properties.space_type',$this->space_type);

        // Parameters

        $parameters = Parameter::where('property_type_id',$this->property_type_id)
            ->where('show_in_request','yes')
            ->get([
                'id',
                'column_name',
                'type',
                'options',
                'required',
                'show_in_request',
                'between_request',
                'multi_request'
            ]);

        if($parameters){
            foreach ($parameters as $key => $value){
                $parameterValue = @$this->paramaters->{$value->column_name};
                if(is_null($parameterValue)) continue;

                switch ($value->type){
                    case 'number':
                        if(
                            $value->between_request == 'yes' &&
                            $this->paramaters->{$value->column_name.'_from'} &&
                            $this->paramaters->{$value->column_name.'_to'}
                        ){
                            $property->whereRaw('(property_parameters.'.$value->column_name.' BETWEEN ? AND ?)',[$this->paramaters->{$value->column_name.'_from'},$this->paramaters->{$value->column_name.'_to'}]);
                        }else{
                            $property->where(function($query) use ($parameterValue, $value) {
                                $query->where('property_parameters.'.$value->column_name,$parameterValue)
                                    ->orWhereNull('property_parameters.'.$value->column_name);
                            });
                        }
                        break;

                    case 'select':
                        if($value->multi_request == 'yes'){
                            $property->whereRaw("(
                              CONCAT(\",\", property_parameters.".$value->column_name." , \",\") REGEXP \",(" . str_replace(',','|', $parameterValue) . "),\" OR
                              (property_parameters.".$value->column_name." IS NULL)
                            )");
                        }else{
                            $property->where(function($query) use ($parameterValue, $value) {
                                $query->where('property_parameters.'.$value->column_name,$parameterValue)
                                    ->orWhereNull('property_parameters.'.$value->column_name);
                            });
                        }
                        break;

                    case 'radio':
                        $property->where(function($query) use ($parameterValue, $value) {
                            $query->where('property_parameters.'.$value->column_name,$parameterValue)
                                ->orWhereNull('property_parameters.'.$value->column_name);
                        });
                        break;

                    case 'multi_select':
                    case 'checkbox':
                        $property->whereRaw("(  
                            (CONCAT(\",\", property_parameters.".$value->column_name." , \",\") REGEXP \",(" . str_replace(',','|', $parameterValue) . "),\"  ) OR
                            (property_parameters.".$value->column_name." IS NULL)
                        )");
                        break;
                }
            }

        }

        return $property;
    }



    public function calls(){
        return $this->morphMany('App\Models\Call','sign')
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

    public function main_paramaters(){
        return $this->hasMany('App\Models\Parameter','property_type_id','property_type_id')
            ->orderBy('position','ASC');
    }


    public function paramaters(){
        return $this->hasOne('App\Models\RequestParameter','request_id');
    }

    public function share_staff(){
        return $this->belongsTo('App\Models\Staff','sharing_staff_id');
    }

}