<?php

namespace Tests\ResponderTests;

use Jerodev\PhpIrcClient\IrcChannel;
use App\Irc\Responders\QuestionResponderEN;
use App\Irc\Responders\QuestionResponderNL;
use Tests\TestCase;

class QuestionResponderTest extends TestCase
{
    public function testFirstResponse()
    {
        $responderEN = new QuestionResponderEN();
        $responderNL = new QuestionResponderNL();

        $responseEN = $responderEN->handlePrivmsg('', new IrcChannel('#channel'), '!can this be tested?');
        $responseNL = $responderNL->handlePrivmsg('', new IrcChannel('#channel'), '!kan dit getest worden?');

        $this->assertNotNull($responseEN);
        $this->assertNotNull($responseNL);
    }

    public function testNonCommand()
    {
        $responderEN = new QuestionResponderEN();
        $responderNL = new QuestionResponderNL();

        $responseEN = $responderEN->handlePrivmsg('', new IrcChannel('#channel'), '!foo');
        $responseNL = $responderNL->handlePrivmsg('', new IrcChannel('#channel'), '!foo');

        $this->assertNull($responseEN);
        $this->assertNull($responseNL);
    }

    public function testNoResponse()
    {
        $responderEN = new QuestionResponderEN();
        $responderNL = new QuestionResponderNL();

        $responseEN = $responderEN->handlePrivmsg('', new IrcChannel('#channel'), '!foo', false);
        $responseNL = $responderNL->handlePrivmsg('', new IrcChannel('#channel'), '!foo', false);

        $this->assertNull($responseEN);
        $this->assertNull($responseNL);
    }

    public function testCache()
    {
        $responderEN = new QuestionResponderEN();
        $responderNL = new QuestionResponderNL();

        $responseEN1 = $responderEN->handlePrivmsg('', new IrcChannel('#channel'), '!can this be tested?');
        $responseNL1 = $responderNL->handlePrivmsg('', new IrcChannel('#channel'), '!kan dit getest worden?');
        
        usleep(100);
        
        $responseEN2 = $responderEN->handlePrivmsg('', new IrcChannel('#channel'), '!can this be tested?');
        $responseNL2 = $responderNL->handlePrivmsg('', new IrcChannel('#channel'), '!kan dit getest worden?');
        
        $this->assertEquals($responseEN1, $responseEN2);
        $this->assertEquals($responseNL1, $responseNL2);
    }
}