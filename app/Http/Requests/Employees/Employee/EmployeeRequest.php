<?php

namespace plunner\Http\Requests\Employees\Employee;

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
            'name' => 'sometimes|required|min:1|max:255',
            'password' => 'sometimes|required|confirmed|min:6',
        ];
    }
}
