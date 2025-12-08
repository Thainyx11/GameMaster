<?php

use Illuminate\Foundation\Testing\DatabaseTruncation;

pest()->extend(Tests\DuskTestCase::class)
    ->use(DatabaseTruncation::class)
    ->in('Browser');