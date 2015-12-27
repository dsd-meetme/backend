<?php

namespace plunner\Http\Requests;

use plunner\Http\Requests\Request;

class PlannerRequest extends Request
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
            'planner_id' => 'sometimes|required|integer'
        ];
    }
}
