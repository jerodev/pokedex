<?php

namespace App\Irc\Responders;

use Illuminate\Support\Facades\Cache;
use Jerodev\PhpIrcClient\IrcChannel;

/**
 *  Answers simple yes/no questions
 */
abstract class QuestionResponder extends Responder
{
    /** @var string[] */
    protected $answers;

    /** @var string[] */
    protected $prefixes;

    public function handlePrivmsg(string $from, IrcChannel $to, string $message, bool $respond = true): ?string
    {
        if ($respond === false || !in_array(strstr($message, ' ', true), $this->prefixes)) {
            return null;
        }

        return Cache::remember($message, 300, function () {
            return $this->answers[array_rand($this->answers)];
        });
    }
}