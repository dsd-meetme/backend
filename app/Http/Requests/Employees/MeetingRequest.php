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
        //TODO fix this with the new data
        return [
            'title' => 'required|max:255|unique:meetings,title,'.$this->route('meetings').',id',
            'description' => 'required|max:255',
            'start_time' => 'required|date_format:Y-m-d H:i:s',
            'end_time' => 'required|date_format:Y-m-d H:i:s|after:meeting_start', //TODO I think taht the correct way is datae_format, but we have to define this with the frontend guys and test it with the database, we have also to consider timezone
            'repeat' => 'required|integer', //TODO we skip this functionality
            'repetition_end_time' => 'date_format:Y-m-d', //TODO we skip this functionality
            'is_scheduled' => 'required|boolean',
            'group_id' => 'required|exists:groups,id',
            'employee_id' => 'required|exists:employees,id',
        ];
    }
}
