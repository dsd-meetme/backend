<?php

namespace plunner\Http\Requests\Companies\Groups;

use plunner\Company;
use plunner\Http\Requests\Request;

/**
 * Class EmployeeRequest
 * @package plunner\Http\Requests\Companies\Groups
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
            'id' => 'required|array|exists:employees,id,company_id,'.$this->user()->id,
        ];
    }
}
