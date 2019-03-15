<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

abstract class Repository
{
    protected function query(string $table)
    {
        return DB::table($table);
    }

    protected function raw($value)
    {
        return DB::raw($value);
    }
}
