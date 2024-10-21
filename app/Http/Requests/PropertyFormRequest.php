<?php

namespace App\Http\Requests;
use App\Models\Parameter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PropertyFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->segment(3);
        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST': {

                // Property Validation
                $validation = [
                    'key'=> 'required|string',
                    'importer_data_id'=> [
                        'nullable',
                        'int',
                        Rule::exists('importer_data','id')->where(function ($query) {
                            $query->whereNull('property_id');
                        })
                    ],
                    'property_type_id' => 'required|int|exists:property_types,id',
                    'property_model_id' => 'nullable|int|exists:property_model,id',
                    'purpose_id' => 'required|int|exists:purposes,id',
                    'data_source_id' => 'required|int|exists:data_sources,id',
                    'area_id' => 'required|int|exists:areas,id',

                    'building_number'=> 'nullable|string',
                    'flat_number'=> 'nullable|string',

                    'property_status_id' =>  'required|int|exists:property_status,id',
                    'name'=> 'nullable|string',
                    'description'=> 'nullable|string',
                    'remarks'=> 'nullable|string',
                    'payment_type'=> 'required|string|in:cash,installment,cash_installment',
                    'price'=> 'required|numeric',
                    'currency'=> 'required|string|in:EGP,USD',
                    'negotiable'=> 'required|string|in:yes,no',
                    'space'=> 'required|numeric',
                    'address'=> 'required|string',
                    'latitude'=> 'nullable|numeric',
                    'longitude'=> 'nullable|numeric',
                    'video_url'=> 'nullable|string|url',
                    'sales_id' => [
                        'required',
                        'int',
                        Rule::exists('staff','id')->where(function ($query) {
                            $query->whereIn('permission_group_id',explode(',',setting('sales_group')));
                        })
                    ],
                ];

                if(!$this->importer_data_id){
                    $validation['client_id'] = 'required|int|exists:clients,id';
                }

                if(in_array($this->payment_type,['installment','cash_installment'])){
                    $validation['deposit'] = 'required|numeric';
                    $validation['years_of_installment'] = 'required|numeric';
                }

                // Parameters Validation

                $parametersData = Parameter::where('property_type_id',$this->property_type_id)
                    ->get([
                        'column_name',
                        'type',
                        'options',
                        'required'
                    ]);
                if($parametersData->isNotEmpty()){
                    foreach ($parametersData as $key => $value){
                        $ruleData      = [];
                        $ruleDataArray = [];

                        // Required
                        if($value->required == 'yes'){
                            $ruleData[]= 'required';
                        }else{
                            $ruleData[]= 'nullable';
                        }
                        switch ($value->type){

                            case 'text':
                            case 'textarea':
                                $ruleData[]= 'string';
                                break;

                            case 'number':
                                $ruleData[]= 'numeric';
                                break;

                            case 'select':
                            case 'radio':
                                $ruleData[]= 'string';
                                $ruleData[]= 'in:'.implode(',',array_column($value->options,'value'));
                                break;

                            case 'multi_select':
                            case 'checkbox':
                                $ruleData[]= 'array';
                                $ruleDataArray[] = 'string';
                                $ruleDataArray[]= 'in:'.implode(',',array_column($value->options,'value'));
                                break;
                        }

                        $validation['p_'.$value->column_name] = implode('|',$ruleData);

                        if(!empty($ruleDataArray)){
                            $validation['p_'.$value->column_name.'.*'] = implode('|',$ruleDataArray);
                        }
                    }
                }


                if(in_array($this->property_status_id,explode(',','archive_property_status'))){
                    $validation['hold_until'] = 'required|date_format:"Y-m-d"';
                }


                return $validation;


            }
            case 'PUT':
            case 'PATCH':
            {

                // Property Validation
                $validation = [
                    'key'=> 'required|string',
                    'property_type_id' => 'required|int|exists:property_types,id',
                    'property_model_id' => 'nullable|int|exists:property_model,id',
                    'purpose_id' => 'required|int|exists:purposes,id',
                    'data_source_id' => 'required|int|exists:data_sources,id',
                    'client_id' => [
                        'required',
                        'int',
                        Rule::exists('clients','id')->where(function ($query) {
                            $query->where('type','investor');
                        })
                    ],
                    'area_id' => 'required|int|exists:areas,id',

                    'building_number'=> 'nullable|string',
                    'flat_number'=> 'nullable|string',

                    'property_status_id' =>  'required|int|exists:property_status,id',
                    'name'=> 'nullable|string',
                    'description'=> 'nullable|string',
                    'remarks'=> 'nullable|string',
                    'payment_type'=> 'required|string|in:cash,installment,cash_installment',
                    'price'=> 'required|numeric',
                    'currency'=> 'required|string|in:EGP,USD',
                    'negotiable'=> 'required|string|in:yes,no',
                    'space'=> 'required|numeric',
                    'address'=> 'required|string',
                    'latitude'=> 'nullable|numeric',
                    'longitude'=> 'nullable|numeric',
                    'video_url'=> 'nullable|string|url',
                    'sales_id' => [
                        'required',
                        'int',
                        Rule::exists('staff','id')->where(function ($query) {
                            $query->whereIn('permission_group_id',explode(',',setting('sales_group')));
                        })
                    ],
                ];
                if(in_array($this->payment_type,['installment','cash_installment'])){
                    $validation['deposit'] = 'required|numeric';
                    $validation['years_of_installment'] = 'required|numeric';
                }

                // Parameters Validation

                $parametersData = Parameter::where('property_type_id',$this->property_type_id)
                    ->get([
                        'column_name',
                        'type',
                        'options',
                        'required'
                    ]);
                if($parametersData->isNotEmpty()){
                    foreach ($parametersData as $key => $value){
                        $ruleData      = [];
                        $ruleDataArray = [];

                        // Required
                        if($value->required == 'yes'){
                            $ruleData[]= 'required';
                        }else{
                            $ruleData[]= 'nullable';
                        }
                        switch ($value->type){

                            case 'text':
                            case 'textarea':
                                $ruleData[]= 'string';
                                break;

                            case 'number':
                                $ruleData[]= 'numeric';
                                break;

                            case 'select':
                            case 'radio':
                                $ruleData[]= 'string';
                                $ruleData[]= 'in:'.implode(',',array_column($value->options,'value'));
                                break;

                            case 'multi_select':
                            case 'checkbox':
                                $ruleData[]= 'array';
                                $ruleDataArray[] = 'string';
                                $ruleDataArray[]= 'in:'.implode(',',array_column($value->options,'value'));
                                break;
                        }

                        $validation['p_'.$value->column_name] = implode('|',$ruleData);

                        if(!empty($ruleDataArray)){
                            $validation['p_'.$value->column_name.'.*'] = implode('|',$ruleDataArray);
                        }
                    }
                }

                if(in_array($this->property_status_id,explode(',','archive_property_status'))){
                    $validation['hold_until'] = 'required|date_format:"Y-m-d"';
                }

                return $validation;


            }
            default:break;
        }

    }
}
