<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    function truncate()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('staff')->truncate();
        DB::table('rotas')->truncate();
        DB::table('shifts')->truncate();
        DB::table('shops')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
