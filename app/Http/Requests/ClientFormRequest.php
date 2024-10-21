<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class ClientFormRequest extends FormRequest
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

    public function messages(){
        return [
            'mobile1.unique'=> __('The mobile has already been taken :name',['name'=>getClientByMobile($this->mobile1)]),
            'mobile2.unique'=> __('The mobile has already been taken :name',['name'=>getClientByMobile($this->mobile2)])
        ];
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
                $validation = [
                    'type'     =>  'required|string|in:client,investor',
                    'name'      =>  'required|string',
                    'email'         =>  'nullable|string|email|unique:clients,email',
                    'phone'         =>  'nullable|numeric|unique:clients,phone',
                    'mobile1'        =>  'required|numeric|unique:clients,mobile1',
                    'mobile2'        =>  'nullable|numeric|unique:clients,mobile2',
                    'fax'         =>  'nullable|numeric|unique:clients,fax',
                    'website'        =>  'nullable|active_url',
                    'address'        =>  'nullable|string',
                    'description'     =>  'nullable|string',
                    'status'        =>  'required|string|in:active,in-active'
                ];

                if($this->type == 'investor'){
                    $validation['investor_type'] = 'required|string|in:individual,company,broker';
                    if(in_array($this->investor_type,['company','broker'])){
                        $validation['company_name'] = 'required|string';
                    }
                }

                return $validation;

            }
            case 'PUT':
            case 'PATCH':
            {
                $validation = [
                    'type'     =>  'required|string|in:client,investor',
                    'name'      =>  'required|string',
                    'email'         =>  'nullable|string|email|unique:clients,email,'.$id,
                    'phone'         =>  'nullable|numeric|unique:clients,phone,'.$id,
                    'mobile1'        =>  'required|numeric|unique:clients,mobile1,'.$id,
                    'mobile2'        =>  'nullable|numeric|unique:clients,mobile2,'.$id,
                    'fax'         =>  'nullable|numeric|unique:clients,fax,'.$id,
                    'website'        =>  'nullable|active_url',
                    'address'        =>  'nullable|string',
                    'description'     =>  'nullable|string',
                    'status'        =>  'required|string|in:active,in-active'
                ];

                if($this->type == 'investor'){
                    $validation['investor_type'] = 'required|string|in:individual,company,broker';
                    if(in_array($this->investor_type,['company','broker'])){
                        $validation['company_name'] = 'required|string';
                    }
                }

                return $validation;
            }
            default:break;
        }

    }


}
