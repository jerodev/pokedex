<?php

namespace App\Repositories;

class UserRepository extends Repository
{
    /** @var string */
    const table = 'users';

    public static function getUserId(string $nickname): int
    {
        $id = parent::query(self::table)
            ->whereNickname($nickname)
            ->orWhereExists(function ($query) use ($nickname) {
                $query->from('aliases')
                    ->whereColumn(self::table.'.id', 'aliases.user_id')
                    ->whereNickname($nickname)
                    ->select(parent::raw(1));
            })
            ->pluck('id')
            ->first();
        if ($id === null) {
            $id = parent::query(self::table)->insertGetId(['nickname' => $nickname]);
        }

        return $id;
    }
}
