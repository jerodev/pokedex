<?php

namespace App\Irc\Responders;

use App\Irc\Response;
use Jerodev\PhpIrcClient\IrcChannel;

/**
 *  `!news {topic}`
 *  Returns the latest news message for a specific topic using the Google news rss feed.
 */
class NewsResponder extends Responder
{
    public function handlePrivmsg(string $from, IrcChannel $to, string $message, bool $respond = true): ?Response
    {
        if ($respond === false || strstr($message, ' ', true) !== '!news') {
            return null;
        }

        $payload = urlencode(trim(strstr($message, ' ')));
        if (empty($payload)) {
            return null;
        }

        return $this->throttle("news_$from", 120, 1, function () use ($payload) {
            $xml = json_decode(json_encode(simplexml_load_string(file_get_contents("https://news.google.com/rss/search?q=$payload&hl=nl&gl=BE&ceid=BE:nl"))));

            $news = null;
            if (isset($xml->channel) && isset($xml->channel->item)) {
                foreach ($xml->channel->item as $item) {
                    if ($news === null || strtotime($news->pubDate) < strtotime($item->pubDate)) {
                        $news = $item;
                    }
                }
            }

            if ($news !== null) {
                return new Response(date('[Y-m-d H:i]', strtotime($news->pubDate)) . " $news->title\n$news->link");
            } else {
                return new Response('No news found for category `' . urldecode($payload) . '`.');
            }
        }, $from);
    }
}
