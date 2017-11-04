<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function create(string $class, array $attributes = [])
    {
        return factory($class)->create($attributes);
    }
}
