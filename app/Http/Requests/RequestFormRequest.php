<?php

namespace App\Http\Requests;
use App\Models\Parameter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RequestFormRequest extends FormRequest
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
                    'property_type_id' => 'required|int|exists:property_types,id',
                    'purpose_id' => 'required|int|exists:purposes,id',
                    'data_source_id' => 'required|int|exists:data_sources,id',
                    'client_id' => [
                        'required',
                        'int',
                        Rule::exists('clients','id')->where(function ($query) {
                            $query->where('type','client');
                        })
                    ],
                    'area_ids' => 'required|array',
                    'area_ids.*' => 'required|int|exists:areas,id',
                    'request_status_id' =>  'required|int|exists:request_status,id',
                    'name'=> 'nullable|string',
                    'description'=> 'nullable|string',
                    'payment_type'=> 'required|string|in:cash,installment,cash_installment',
                    'price_from'=> 'required|numeric',
                    'price_to'=> 'required|numeric',//|gte:price_from',
                    'currency'=> 'required|string|in:EGP,USD',
                    'property_model_id' => 'nullable|array',
                    'property_model_id.*' => 'nullable|int|exists:property_model,id',
                    'space_from'=> 'required|numeric',
                    'space_to'=> 'required|numeric',//|gte:space_from',
                    'sales_id' => [
                        'required',
                        'int',
                        Rule::exists('staff','id')->where(function ($query) {
                            $query->whereIn('permission_group_id',explode(',',setting('sales_group')));
                        })
                    ],
                ];
                if(in_array($this->payment_type,['installment','cash_installment'])){
                    $validation['deposit_from'] = 'numeric';
                    $validation['deposit_to']   = 'numeric';//|gte:deposit_from';
                }

                // Parameters Validation

                $parametersData = Parameter::where('property_type_id',$this->property_type_id)
                    ->where('show_in_request','yes')
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
                        //if($value->required == 'yes'){
                        //    $ruleData[]= 'required';
                        //}else{
                            $ruleData[]= 'nullable';
                        //}
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
                                if($value->multi_request == 'yes'){
                                    $ruleData[]= 'array';
                                    $ruleDataArray[] = 'string';
                                    $ruleDataArray[]= 'in:'.implode(',',array_column($value->options,'value'));
                                }else{
                                    $ruleData[]= 'string';
                                    $ruleData[]= 'in:'.implode(',',array_column($value->options,'value'));
                                }

                                break;

                            case 'multi_select':
                            case 'checkbox':
                                $ruleData[]= 'array';
                                $ruleDataArray[] = 'string';
                                $ruleDataArray[]= 'in:'.implode(',',array_column($value->options,'value'));
                                break;
                        }

                        if($value->type == 'number' && $value->between_request == 'yes'){
                            $validation['p_'.$value->column_name.'_from'] = implode('|',$ruleData);
                            $validation['p_'.$value->column_name.'_to'] = implode('|',$ruleData);//.'|gte:p_'.$value->column_name.'_from';

                        }else{
                            $validation['p_'.$value->column_name] = implode('|',$ruleData);

                            if(!empty($ruleDataArray)){
                                $validation['p_'.$value->column_name.'.*'] = implode('|',$ruleDataArray);
                            }
                        }


                    }
                }

                return $validation;


            }
            case 'PUT':
            case 'PATCH':
            {
                // Property Validation
                $validation = [
                    'property_type_id' => 'required|int|exists:property_types,id',
                    'purpose_id' => 'required|int|exists:purposes,id',
                    'data_source_id' => 'required|int|exists:data_sources,id',
                    'client_id' => [
                        'required',
                        'int',
                        Rule::exists('clients','id')->where(function ($query) {
                            $query->where('type','client');
                        })
                    ],
                    'area_ids' => 'required|array',
                    'area_ids.*' => 'required|int|exists:areas,id',
                    'request_status_id' =>  'required|int|exists:request_status,id',
                    'name'=> 'nullable|string',
                    'description'=> 'nullable|string',
                    'payment_type'=> 'required|string|in:cash,installment,cash_installment',
                    'price_from'=> 'required|numeric',
                    'price_to'=> 'required|numeric',//|gte:price_from',
                    'currency'=> 'required|string|in:EGP,USD',
                    'property_model_id' => 'nullable|array',
                    'property_model_id.*' => 'nullable|int|exists:property_model,id',
                    'space_from'=> 'required|numeric',
                    'space_to'=> 'required|numeric',//|gte:space_from',
                    'sales_id' => [
                        'required',
                        'int',
                        Rule::exists('staff','id')->where(function ($query) {
                            $query->whereIn('permission_group_id',explode(',',setting('sales_group')));
                        })
                    ],
                ];
                if(in_array($this->payment_type,['installment','cash_installment'])){
                    $validation['deposit_from'] = 'numeric';
                    $validation['deposit_to']   = 'numeric';//|gte:deposit_from';
                }

                // Parameters Validation

                $parametersData = Parameter::where('property_type_id',$this->property_type_id)
                    ->where('show_in_request','yes')
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
                        //if($value->required == 'yes'){
                        //    $ruleData[]= 'required';
                        //}else{
                            $ruleData[]= 'nullable';
                        //}
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
                                if($value->multi_request == 'yes'){
                                    $ruleData[]= 'array';
                                    $ruleDataArray[] = 'string';
                                    $ruleDataArray[]= 'in:'.implode(',',array_column($value->options,'value'));
                                }else{
                                    $ruleData[]= 'string';
                                    $ruleData[]= 'in:'.implode(',',array_column($value->options,'value'));
                                }

                                break;

                            case 'multi_select':
                            case 'checkbox':
                                $ruleData[]= 'array';
                                $ruleDataArray[] = 'string';
                                $ruleDataArray[]= 'in:'.implode(',',array_column($value->options,'value'));
                                break;
                        }

                        if($value->type == 'number' && $value->between_request == 'yes'){
                            $validation['p_'.$value->column_name.'_from'] = implode('|',$ruleData);
                            $validation['p_'.$value->column_name.'_to'] = implode('|',$ruleData);//.'|gte:p_'.$value->column_name.'_from';

                        }else{
                            $validation['p_'.$value->column_name] = implode('|',$ruleData);

                            if(!empty($ruleDataArray)){
                                $validation['p_'.$value->column_name.'.*'] = implode('|',$ruleDataArray);
                            }
                        }


                    }
                }

                return $validation;

            }
            default:break;
        }

    }
}
