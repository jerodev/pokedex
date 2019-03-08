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
                'command' => $command
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

    public static function learnFact(string $nickname, string $channel, string $command, string $response): void
    {
        $channelid = ChannelRepository::getChannelId($channel);
        $userid = UserRepository::getUserId($nickname);

        parent::query(self::table)->insert([
            'channel_id' => $channelid,
            'user_id' => $userid,
            'command' => $command,
            'response' => $response
        ]);
    }
}