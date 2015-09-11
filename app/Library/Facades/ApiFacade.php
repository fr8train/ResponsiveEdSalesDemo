<?php
/**
 * Created by PhpStorm.
 * User: tyler
 * Date: 9/7/15
 * Time: 5:27 PM
 */

namespace App\Library\Facades;

use Illuminate\Support\Facades\Facade;

class ApiFacade extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'api';
    }

}