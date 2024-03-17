<?php 

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\Helpers\Utils as UtilsHelper;

class Utils extends Facade
{
    protected static function getFacadeAccessor()
    {
        return UtilsHelper::class;
    }
}