<?php

namespace plunner\Http\Requests\Companies\Employees;

use plunner\Company;
use plunner\Http\Requests\Request;

class EmployeeRequest extends Request
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
        return [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:employees,email,'.$this->route('employees').',id,company_id,'.$this->user()->id,
            'password' => 'required|confirmed|min:6',
        ];
    }
}
