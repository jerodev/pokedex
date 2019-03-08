<?php

namespace App\Repositories;

class MessageRepository extends Repository
{
    const table = 'messages';

    public static function logMessage(string $channel, string $nickname, string $message): void
    {
        $channelid = ChannelRepository::getChannelId($channel);
        $userid = UserRepository::getUserId($nickname);

        parent::query(self::table)->insert([
            'channel_id' => $channelid,
            'user_id' => $userid,
            'nickname' => $nickname,
            'message' => $message,
        ]);
    }
}