<?php

use Isofman\LaravelRestier\Restier;

if (! function_exists('restier')) {
    /**
    * @return Restier
    */
    function restier()
    {
        return app('restier');
    }
}