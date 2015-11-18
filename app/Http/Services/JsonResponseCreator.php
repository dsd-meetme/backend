<?php

/**
 * Created by PhpStorm.
 * User: miha
 * Date: 18.11.15.
 * Time: 19:45
 */
class JsonResponseCreator implements ResponseCreator
{
    public function respond($data, $code)
    {
        return response()->toJson(
            $data,
            $code
        );
    }
}