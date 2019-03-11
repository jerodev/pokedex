<?php

namespace App\Repositories;

class FactRepository extends Repository
{
    const table = 'facts';

    public static function getResponseString(string $command, string $channel, bool $updateUseCount = false): ?string
    {
        $channelid = ChannelRepository::getChannelId($channel);

        $fact = parent::query(self::table)
            ->where([
                'channel_id' => $channelid,
                'command'    => $command,
            ])
            ->inRandomOrder()
            ->select('id', 'response')
            ->first();

        if ($fact) {
            parent::query(self::table)->where('id', $fact->id)->increment('uses');

            return $fact->response;
        }

        return null;
    }

    public static function getSingleStats(string $command, string $channel)
    {
        $channelid = ChannelRepository::getChannelId($channel);

        return parent::query(self::table)
            ->where([
                'channel_id' => $channelid,
                'command'    => $command,
            ])
            ->groupBy('command')
            ->select(
                self::raw('MIN(created_at) as created_at'),
                self::raw('SUM(uses) as uses'),
                self::raw('COUNT(1) as response_count')
            )
            ->first();
    }

    public static function getStats(string $channel)
    {
        $channelid = ChannelRepository::getChannelId($channel);

        $fact = parent::query(self::table)
            ->join('users', 'users.id', '=', self::table.'.user_id')
            ->where('channel_id', $channelid)
            ->select(
                'created_at',
                'users.nickname',
                'command'
            )
            ->orderByDesc('created_at')
            ->first();

        $fact->fact_count = parent::query(self::table)->where('channel_id', $channelid)->count();

        return $fact;
    }

    public static function learnFact(string $nickname, string $channel, string $command, string $response): void
    {
        $channelid = ChannelRepository::getChannelId($channel);
        $userid = UserRepository::getUserId($nickname);

        parent::query(self::table)->insert([
            'channel_id' => $channelid,
            'user_id'    => $userid,
            'command'    => $command,
            'response'   => $response,
        ]);
    }
}
