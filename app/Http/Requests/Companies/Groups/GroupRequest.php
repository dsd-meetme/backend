<?php

namespace plunner\Http\Requests\Companies\Groups;

use plunner\Http\Requests\Request;

/**
 * Class GroupRequest
 * @package plunner\Http\Requests\Companies\Groups
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 */
class GroupRequest extends Request
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
            'name' => 'required|max:255|unique:groups,name,'.$this->route('groups').',id,company_id,'.$this->user()->id,
            'description' => 'max:255',
            'planner_id' => 'required|exists:employees,id,company_id,'.$this->user()->id,
        ];
    }
}
