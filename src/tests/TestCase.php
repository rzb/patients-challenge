<?php

namespace Tests;

use Generator;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function providePagination(): Generator
    {
        yield 'Specified limit is used' => [5, 5];

        yield 'Default limit is used when not specified' => [15, null];
    }
}
