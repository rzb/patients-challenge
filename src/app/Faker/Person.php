<?php

namespace App\Faker;

use Faker\Provider\Base;

class Person extends Base
{
    // @todo generate a valid cns
    public static function cns(): string
    {
        return static::numerify(str_repeat('#', 15));
    }
}
