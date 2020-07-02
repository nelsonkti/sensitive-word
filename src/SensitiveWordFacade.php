<?php


namespace Nelsonkti\SensitiveWord;

use Illuminate\Support\Facades\Facade;

class SensitiveWordFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Nelsonkti\SensitiveWord\SensitiveWord';
    }
}
