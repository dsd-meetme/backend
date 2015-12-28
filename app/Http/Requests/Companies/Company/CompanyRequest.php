<?php

namespace plunner\Http\Requests\Companies\Company;

use plunner\Http\Requests\Request;

class CompanyRequest extends Request
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
            'name' => 'sometimes|required|min:1|max:255|unique:companies,name,' . $this->user()->id,
            'password' => 'sometimes|required|confirmed|min:6',
        ];
    }
}
