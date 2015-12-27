<?php

namespace plunner\Http\Controllers\Companies\Company;

use plunner\Http\Controllers\Controller;
use plunner\Http\Requests\Companies\Company\CompanyRequest;


class CompanyController extends Controller
{
    public function __construct()
    {
        config(['auth.model' => \plunner\Company::class]);
        config(['jwt.user' => \plunner\Company::class]);
        $this->middleware('jwt.authandrefresh:mode-cn');
    }

    /**
     * Display the company data
     * /company/company/
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company = \Auth::user();
        return $company;
    }


    /**
     * update the company name and password (both optionally)
     * @param CompanyRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(CompanyRequest $request)
    {
        $company = \Auth::user();
        $input = $request->only(['name', 'password']);
        $company->update($input);
        return $company;
    }
}
