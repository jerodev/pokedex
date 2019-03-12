<?php

namespace App\Repositories;

use Illuminate\Database\Query\Builder as QueryBuilder;

class FactRepository extends Repository
{
    /** @var string */
    const table = 'facts';

    public static function getResponseString(string $command, string $channel, bool $updateUseCount = false): ?string
    {
        $fact = self::getCommandQuery($command, $channel)
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
        return self::getCommandQuery($command, $channel)
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
        $fact = parent::query(self::table)
            ->join('users', 'users.id', '=', self::table.'.user_id')
            ->whereExists(function ($query) use ($channel) {
                $query->from('channels')
                    ->whereColumn('channels.id', '=', 'facts.channel_id')
                    ->where('channels.name', $channel)
                    ->select(self::raw(1));
            })
            ->select(
                'created_at',
                'users.nickname',
                'command'
            )
            ->orderByDesc('created_at')
            ->first();

        $fact->fact_count = parent::query(self::table)->whereExists(function ($query) use ($channel) {
                $query->from('channels')
                    ->whereColumn('channels.id', '=', 'facts.channel_id')
                    ->where('channels.name', $channel)
                    ->select(self::raw(1));
            })->count();

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

    /**
     *  Create a query builder that filters on command and channel.
     *
     *  @param string $command
     *  @param string $channel
     *
     *  @return QueryBuilder
     */
    private static function getCommandQuery(string $command, string $channel): QueryBuilder
    {
        return parent::query(self::table)
            ->where('command', $command)
            ->whereExists(function ($query) use ($channel) {
                $query->from('channels')
                    ->whereColumn('channels.id', '=', 'facts.channel_id')
                    ->where('channels.name', $channel)
                    ->select(self::raw(1));
            });
    }
}
