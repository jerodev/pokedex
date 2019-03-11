<?php

namespace Tests\ResponderTests;

use Jerodev\PhpIrcClient\IrcChannel;
use App\Irc\Responders\TimeResponder;
use Tests\TestCase;

class TimeResponderTest extends TestCase
{
    public function testFirstResponse()
    {
        $responder = new TimeResponder();

        $response = $responder->handlePrivmsg('', new IrcChannel('#channel'), '!time');
        $this->assertNotNull($response);
    }

    public function testNonCommand()
    {
        $responder = new TimeResponder();

        $response = $responder->handlePrivmsg('', new IrcChannel('#channel'), '!foo', false);
        $this->assertNull($response);
    }

    public function testNoResponse()
    {
        $responder = new TimeResponder();

        $response = $responder->handlePrivmsg('', new IrcChannel('#channel'), '!time', false);
        $this->assertNull($response);
    }
}