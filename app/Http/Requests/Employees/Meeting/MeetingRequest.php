<?php

namespace plunner\Http\Requests\Employees\Meeting;

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
        return [
            'title' => 'required|max:255',
            'description' => 'sometimes|required|max:255',
            'duration' => 'required|integer',
        ];
    }
}
