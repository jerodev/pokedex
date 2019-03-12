<?php

namespace App\Repositories;

class ChannelRepository extends Repository
{
    /** @var string */
    const table = 'channels';

    public static function getChannelId(string $channel): int
    {
        $id = parent::query(self::table)->whereName($channel)->pluck('id')->first();
        if ($id === null) {
            $id = parent::query(self::table)->insertGetId(['name' => $channel]);
        }

        return $id;
    }
}
