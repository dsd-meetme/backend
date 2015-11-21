<?php

/**
 * Created by PhpStorm.
 * User: miha
 * Date: 18.11.15.
 * Time: 19:44
 */

namespace Services\ResponseCreator;

interface ResponseCreator
{
    public function respond($data, $code);
}