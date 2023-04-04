<?php

namespace App\Faker;

use App\Support\Cns;
use Faker\Provider\Base;

class Person extends Base
{
    public static function cns(): string
    {
        return Cns::generate();
    }
}
