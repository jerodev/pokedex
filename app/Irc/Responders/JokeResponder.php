<?php

namespace App\Irc\Responders;

use App\Irc\Response;
use Cache;
use Jerodev\PhpIrcClient\IrcChannel;

/**
 *  `!joke`
 *  Returns a random short joke.
 */
class JokeResponder extends Responder
{
    /** @var int */
    private $jokeLimit = 4;

    /** @var int */
    private $timeLimit = 15;

    public function handlePrivmsg(string $from, IrcChannel $to, string $message, bool $respond = true): ?Response
    {
        if ($respond === false || $message !== '!joke') {
            return null;
        }

        return $this->throttle("joke_$from@" . $to->getName(), $this->timeLimit * 60, $this->jokeLimit, function () {
            $jokes = Cache::rememberForever('jokes', function () {
                $providers = [
                    'getGeekJokeApiJokes',
                    'getJokeDatasetJokes',
                    'getOfficialJokeApiJokes',
                    'getPyJokes',
                    'getYoMamaJokes',
                ];

                $jokes = [];
                foreach ($providers as $provider) {
                    $jokes = array_merge($jokes, $this->{$provider}());
                }

                return $jokes;
            });

            return new Response($jokes[array_rand($jokes)]);
        }, $from, "You can only request $this->jokeLimit jokes every $this->timeLimit minutes!");
    }

    /**
     *  https://github.com/pyjokes/pyjokes.
     */
    private function getPyJokes(): array
    {
        $data = file_get_contents('https://raw.githubusercontent.com/pyjokes/pyjokes/master/pyjokes/jokes_en.py');
        $jokes = [];

        foreach (explode("\n", $data) as $line) {
            if (substr($line, 0, 5) === '    "') {
                $jokes[] = trim($line, '", ');
            }
        }

        return $jokes;
    }

    /**
     *  https://github.com/taivop/joke-dataset.
     */
    private function getJokeDatasetJokes(): array
    {
        $data = json_decode(file_get_contents('https://raw.githubusercontent.com/taivop/joke-dataset/master/reddit_jokes.json'));
        $jokes = [];

        foreach ($data as $joke) {
            if (strlen($joke->body) < 200) {
                $jokes[] = "$joke->title\n$joke->body";
            }
        }

        return $jokes;
    }

    private function getGeekJokeApiJokes(): array
    {
        $data = json_decode(file_get_contents('https://raw.githubusercontent.com/sameerkumar18/geek-joke-api/master/data.json'));
        $jokes = [];

        foreach ($data as $joke) {
            if (strlen($joke) < 200) {
                $jokes[] = $joke;
            }
        }

        return $jokes;
    }

    private function getOfficialJokeApiJokes(): array
    {
        $data = json_decode(file_get_contents('https://raw.githubusercontent.com/15Dkatz/official_joke_api/master/jokes/index.json'));
        $jokes = [];

        foreach ($data as $joke) {
            $jokes[] = "$joke->setup\n$joke->punchline";
        }

        return $jokes;
    }

    private function getYoMamaJokes(): array
    {
        $data = json_decode(file_get_contents('https://raw.githubusercontent.com/joshbuchea/yo-mama/master/jokes.json'));
        $jokes = [];

        foreach ($data as $key => $list) {
            if (is_array($list)) {
                foreach ($list as $joke) {
                    $jokes[] = $joke;
                }
            }
        }

        return $jokes;
    }
}
