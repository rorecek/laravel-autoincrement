<?php
namespace Rorecek\AutoIncrement\Facades;

use Illuminate\Support\Facades\Facade;

class AutoIncrement extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'auto-increment';
    }
}