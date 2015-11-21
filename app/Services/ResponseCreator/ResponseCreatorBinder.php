<?php

/**
 * Created by PhpStorm.
 * User: miha
 * Date: 20.11.15.
 * Time: 15:43
 */

namespace Services\ResponseCreator;

use Illuminate\Support\ServiceProvider;

class ResponseCreatorBinder
{
    /**
     * Register the binding
     *
     * @return void
     */
    public function register()
    {
        $app = $this->app;

        $app->singleton('Services\ServiceCreator\ResponseCreator', function ($app) {
            return new services\serviceCreator\JsonResponseCreator();
        });
    }
}