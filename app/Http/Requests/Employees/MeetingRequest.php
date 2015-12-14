<?php

namespace plunner\Http\Requests\Employees;

use plunner\Http\Requests\Request;

class MeetingRequest extends Request
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
        //TODO test this with the new data
        return [
            'title' => 'required|max:255',
            'description' => 'required|max:255',
            'duration' => 'required|integer',
        ];
    }
}
