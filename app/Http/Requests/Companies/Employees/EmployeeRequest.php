<?php

namespace plunner\Http\Requests\Companies\Employees;

use plunner\Company;
use plunner\Http\Requests\Request;

/**
 * Class EmployeeRequest
 * @package plunner\Http\Requests\Companies\Employees
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 */
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
            'name' => 'required|min:1|max:255',
            'email' => 'required|email|max:255|unique:employees,email,'.$this->route('employees').',id,company_id,'.$this->user()->id,
            'password' => ((\Route::current()->getName() == 'companies.employees.store')?'sometimes|':'').'required|confirmed|min:6',
        ];
    }
}
