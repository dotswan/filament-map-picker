<?php

declare(strict_types=1);

namespace Dotswan\MapPicker\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Dotswan\MapPicker\MapPicker
 */
class MapPicker extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Dotswan\MapPicker\MapPicker::class;
    }
}
