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
        return [
            'title' => 'required|max:255|unique:meetings,title,'.$this->route('meetings').',id',
            'description' => 'required|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:meeting_start', //TODO I think taht the correct way is datae_format, but we have to define this with the frontend guys and test it with the database, we have also to consider timezone
            'repeat' => 'required|integer',
            'repetition_end_time' => 'date',
            'is_scheduled' => 'required|boolean',
            'group_id' => 'required|exists:groups,id',
        ];
    }
}
