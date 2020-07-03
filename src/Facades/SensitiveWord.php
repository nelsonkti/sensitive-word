<?php


namespace Nelsonkti\SensitiveWord\Facades;

use Illuminate\Support\Facades\Facade;

class SensitiveWord extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Nelsonkti\SensitiveWord\SensitiveWord';
    }
}
