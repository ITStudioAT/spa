<?php

use Itstudioat\Spa\Tests\TestCase;

uses(TestCase::class)
    ->beforeEach(function () {
        Route::spa();
    })
    ->in('Feature');
