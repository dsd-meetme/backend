<?php

/**
 * Created by PhpStorm.
 * User: miha
 * Date: 18.11.15.
 * Time: 19:44
 */

namespace services\serviceCreator;

interface ResponseCreator
{
    public function respond($data, $code);
}