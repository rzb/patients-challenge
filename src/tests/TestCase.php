<?php

namespace Tests;

use Generator;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function providePagination(): Generator
    {
        yield 'custom number of results per page' => [5, 5];

        yield 'default number of results per page' => [15, null];
    }
}
