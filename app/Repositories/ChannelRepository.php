<?php

namespace App\Repositories;

class ChannelRepository extends Repository
{
    /** @var string */
    private const table = 'channels';

    public function getChannelId(string $channel): int
    {
        return once(function () use ($channel) {
            $id = parent::query(self::table)->whereName($channel)->pluck('id')->first();
            if ($id === null) {
                $id = parent::query(self::table)->insertGetId(['name' => $channel]);
            }

            return $id;
        });
    }
}
