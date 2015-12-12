<?php
/**
 * Created by PhpStorm.
 * User: claudio
 * Date: 12/12/15
 * Time: 15.41
 */

namespace plunner\Console\Commands\Optimise;


use plunner\company;

/**
 * Class Optimise
 * @author Claudio Cardinale <cardi@thecsea.it>
 * @copyright 2015 Claudio Cardinale
 * @version 1.0.0
 * @package plunner\Console\Commands\Optimise
 */
class Optimise
{
    /**
     * @var Company
     */
    private $company;

    /**
     * Optimise constructor.
     * @param Company $company
     */
    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param Company $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    public function optmise()
    {
        //TODO ...
    }
}