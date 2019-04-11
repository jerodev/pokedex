<?php

namespace App\Irc\Responders;

use App\Irc\Response;
use Jerodev\PhpIrcClient\IrcChannel;

/**
 *  Search for a product in several online stores.
 *
 *  `!amazon {search string}`
 */
class ProductResponder extends Responder
{
    public function handlePrivmsg(string $from, IrcChannel $to, string $message, bool $respond = true): ?Response
    {
        if ($respond === false || strpos($message, ' ') === false) {
            return null;
        }

        $data = explode(' ', $message, 2);
        if (empty(trim($data[1]))) {
            return null;
        }

        switch ($data[0]) {
            case '!amazon':
                return $this->amazonSearch($data[1]);
                break;
				
            case '!bol':
                return $this->bolSearch($data[1]);
                break;
        }

        return null;
    }

    private function amazonSearch(string $search): ?Response
    {
        $search = urlencode($search);

        return new Response("https://www.amazon.de/s/?tag=tabfin0b-21&field-keywords=$search");
    }
	
    private function bolSearch(string $search): ?Response
    {
        $search = urlencode($search);

        return new Response("https://www.bol.com/nl/s/algemeen/zoekresultaten/Ntt/$search");
    }
}
