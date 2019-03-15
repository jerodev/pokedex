<?php

namespace App\Repositories;

use stdClass;

class MessageRepository extends Repository
{
    /** @var string */
    const table = 'messages';

    public static function logMessage(string $channel, string $nickname, string $message): void
    {
        $channelid = ChannelRepository::getChannelId($channel);
        $userid = UserRepository::getUserId($nickname);

        parent::query(self::table)->insert([
            'channel_id' => $channelid,
            'user_id'    => $userid,
            'nickname'   => $nickname,
            'message'    => $message,
        ]);
    }

    public static function getLastUserMessage(string $channel, string $user): ?stdClass
    {
        return parent::query(self::table)
            ->whereExists(function ($query) use ($channel) {
                $query->from('channels')
                    ->whereColumn('channels.id', '=', 'messages.channel_id')
                    ->where('channels.name', $channel)
                    ->select(self::raw(1));
            })
            ->whereExists(function ($query) use ($user) {
                $query->from('users')
                    ->leftJoin('aliases', 'aliases.user_id', '=', 'users.id')
                    ->whereColumn('users.id', '=', 'messages.user_id')
                    ->where(function ($query) use ($user) {
                        $query
                            ->orWhere('users.nickname', $user)
                            ->orWhere('aliases.nickname', $user);
                    })
                    ->select(self::raw(1));
            })
            ->where('created_at', '>', self::raw('(now() - interval 5 minute)'))
            ->latest()
            ->first();
    }
}
