<?php

namespace plunner\Http\Requests;

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
            'title' => 'required|max:255|unique:meetings,name,NULL,id',
            'description' => 'required|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:meeting_start',
            'repeat' => 'required|integer',
            'repetition_end_time' => 'date',
            'is_scheduled' => 'required|boolean',
            'group_id' => 'required|exists:groups,id',
        ];
    }
}
