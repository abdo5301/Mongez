<?php

namespace App\Models;

use App\Libs\AreasData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Property extends Model
{

    use LogsActivity;
    protected static $logAttributes = ['*'];

    protected $table = 'properties';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['call_update','created_at','updated_at','deleted_at'];
    protected $fillable = [
        'property_type_id',
        'purpose_id',
        'data_source_id',
        'client_id',
        'area_id',
        'building_number',
        'flat_number',
        'property_status_id',
        'property_model_id',
        'name',
        'description',
        'remarks',
        'payment_type',
        'years_of_installment',
        'deposit',
        'price',
        'currency',
        'negotiable',
        'space',
        'space_type',
        'address',
        'hold_until',
        'latitude',
        'longitude',
        'video_url',
        'sales_id',
        'created_by_staff_id',
        'importer_data_id',
        'call_update'
    ];


    public function images(){
        return $this->hasMany('App\Models\Image','property_id');
    }

    public function property_type(){
        return $this->belongsTo('App\Models\PropertyType','property_type_id');
    }

    public function property_model(){
        return $this->belongsTo('App\Models\PropertyModel','property_model_id');
    }

    public function purpose(){
        return $this->belongsTo('App\Models\Purpose','purpose_id');
    }

    public function data_source(){
        return $this->belongsTo('App\Models\DataSource','data_source_id');
    }
    public function client(){
        return $this->belongsTo('App\Models\Client','client_id');
    }

    public function area(){
        return $this->belongsTo('App\Models\Area','area_id');
    }

    public function property_status(){
        return $this->belongsTo('App\Models\PropertyStatus','property_status_id');
    }

    public function sales(){
        return $this->belongsTo('App\Models\Staff','sales_id');
    }

    public function created_by_staff(){
        return $this->belongsTo('App\Models\Staff','created_by_staff_id');
    }

    public function importer(){
        return $this->belongsTo('App\Models\Importer','importer_data_id');
    }

    public function main_paramaters(){
        return $this->hasMany('App\Models\Parameter','property_type_id','property_type_id')
            ->orderBy('position','ASC');
    }

    public function paramaters(){
        return $this->hasOne('App\Models\PropertyParameter','property_id');
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


    public function requests(){

        // Request
         $request = Request::join('request_parameters','request_parameters.request_id','=','requests.id')
             ->where([
                 ['requests.property_type_id',$this->property_type_id],
                 ['requests.purpose_id',$this->purpose_id]
             ])
             ->where(function($query){
                 $areas = AreasData::getAreasUp($this->area_id);
                 foreach ($areas as $key => $value){
                     $query->orWhereRaw('FIND_IN_SET('.$value.',requests.area_ids)');
                 }
             })
             ->whereIn('requests.request_status_id',explode(',',setting('available_request_status')));

         // Payment Type
         if($this->payment_type == 'cash'){
             $request->whereIn('requests.payment_type',['cash','cash_installment']);
         }elseif($this->payment_type == 'installment'){
             $request->whereIn('requests.payment_type',['installment','cash_installment']);
         }

         // Deposit
        if(in_array($this->payment_type,['installment','cash_installment'])){
            $request->whereRaw('(? BETWEEN requests.deposit_from AND requests.deposit_to)',[$this->deposit]);
        }

        $request->whereRaw('(? BETWEEN requests.price_from AND requests.price_to)',[$this->price]);
        $request->where('requests.currency',$this->currency);

        $request->whereRaw('(? BETWEEN requests.space_from AND requests.space_to)',[$this->space]);
        $request->where('requests.space_type',$this->space_type);

        // Request Parameter

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
                $parameterValue = $this->paramaters->{$value->column_name};
                if(is_null($parameterValue)) continue;

                switch ($value->type){
                    case 'number':
                        if($value->between_request == 'yes'){
                            $request->whereRaw('( 
                                (? BETWEEN request_parameters.'.$value->column_name.'_from AND request_parameters.'.$value->column_name.'_to) OR 
                                (request_parameters.'.$value->column_name.'_from IS NULL AND request_parameters.'.$value->column_name.'_to IS NULL) 
                            )',[$parameterValue]);
                        }else{
                            $request->where(function($query) use($value,$parameterValue){
                                $query->where('request_parameters.'.$value->column_name,$parameterValue)
                                    ->orWhereNull('request_parameters.'.$value->column_name);
                            });
                        }
                        break;
                    case 'select':
                        if($value->multi_request == 'yes'){
                            $request->whereRaw("(
                                (FIND_IN_SET(\"$parameterValue\",request_parameters.".$value->column_name.") OR
                                (request_parameters.".$value->column_name." IS NULL)
                            )");
                        }else{
                            $request->where(function($query) use($value,$parameterValue){
                                $query->where('request_parameters.'.$value->column_name,$parameterValue)
                                    ->orWhereNull('request_parameters.'.$value->column_name);
                            });
                        }
                        break;
                    case 'radio':
                        $request->where(function($query) use($value,$parameterValue){
                            $query->where('request_parameters.'.$value->column_name,$parameterValue)
                                ->orWhereNull('request_parameters.'.$value->column_name);
                        });
                        break;

                    case 'multi_select':
                    case 'checkbox':
                        $request->whereRaw(" (
                            ( CONCAT(\",\", request_parameters.".$value->column_name." , \",\") REGEXP \",(" . str_replace(',','|', $parameterValue) . "),\" ) OR
                            (request_parameters.".$value->column_name." IS NULL)
                        )");
                        break;
                }
            }
        }

        return $request;

    }

    public static function requestsForImport($propertyTypeID,$purposeID,$areaID,$space,$price){
        // Request
        $request = Request::join('request_parameters','request_parameters.request_id','=','requests.id')
            ->where([
                ['requests.property_type_id',$propertyTypeID],
                ['requests.purpose_id',$purposeID]
            ])
            ->where(function($query) use ($areaID) {
                $areas = AreasData::getAreasUp($areaID);
                foreach ($areas as $key => $value){
                    $query->orWhereRaw('FIND_IN_SET('.$value.',requests.area_ids)');
                }
            })
            ->whereIn('requests.request_status_id',explode(',',setting('available_request_status')));


        $request->whereRaw('(? BETWEEN requests.price_from AND requests.price_to)',[$price]);
        $request->whereRaw('(? BETWEEN requests.space_from AND requests.space_to)',[$space]);

      /*  // Request Parameter

        $parameters = Parameter::where('property_type_id',$propertyTypeID)
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
                $parameterValue = $this->paramaters->{$value->column_name};
                if(is_null($parameterValue)) continue;

                switch ($value->type){
                    case 'number':
                        if($value->between_request == 'yes'){
                            $request->whereRaw('( 
                                (? BETWEEN request_parameters.'.$value->column_name.'_from AND request_parameters.'.$value->column_name.'_to) OR 
                                (request_parameters.'.$value->column_name.'_from IS NULL AND request_parameters.'.$value->column_name.'_to IS NULL) 
                            )',[$parameterValue]);
                        }else{
                            $request->where(function($query) use($value,$parameterValue){
                                $query->where('request_parameters.'.$value->column_name,$parameterValue)
                                    ->orWhereNull('request_parameters.'.$value->column_name);
                            });
                        }
                        break;
                    case 'select':
                        if($value->multi_request == 'yes'){
                            $request->whereRaw("(
                                (FIND_IN_SET(\"$parameterValue\",request_parameters.".$value->column_name.") OR
                                (request_parameters.".$value->column_name." IS NULL)
                            )");
                        }else{
                            $request->where(function($query) use($value,$parameterValue){
                                $query->where('request_parameters.'.$value->column_name,$parameterValue)
                                    ->orWhereNull('request_parameters.'.$value->column_name);
                            });
                        }
                        break;
                    case 'radio':
                        $request->where(function($query) use($value,$parameterValue){
                            $query->where('request_parameters.'.$value->column_name,$parameterValue)
                                ->orWhereNull('request_parameters.'.$value->column_name);
                        });
                        break;

                    case 'multi_select':
                    case 'checkbox':
                        $request->whereRaw(" (
                            ( CONCAT(\",\", request_parameters.".$value->column_name." , \",\") REGEXP \",(" . str_replace(',','|', $parameterValue) . "),\" ) OR
                            (request_parameters.".$value->column_name." IS NULL)
                        )");
                        break;
                }
            }
        }*/

        return $request;

    }

}