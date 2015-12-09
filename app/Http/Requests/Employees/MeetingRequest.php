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
            'title' => 'required|max:255|unique:groups,name,NULL,id,company_id,',
            'description' => 'required|max:255',
            'meeting_start' => 'required|date',
            'meeting_end' => 'required|date|after:meeting_start',
            'utc' => 'required|integer|max:12|min:-12',
            'repeat' => 'required|integer',
        ];
    }
}
