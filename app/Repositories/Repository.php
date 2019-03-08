<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

abstract class Repository
{
    protected static function query(string $table)
    {
        return DB::table($table);
    }
    
    protected static function raw($value)
    {
        return DB::raw($value);
    }
    
    
}