<?php

namespace Tests\ResponderTests;

use App\Irc\Responders\ProductResponder;
use Jerodev\PhpIrcClient\IrcChannel;
use Tests\TestCase;

class ProductResponderTest extends TestCase
{
    public function testEmptyPayload()
    {
        $responder = new ProductResponder();
        $response = $responder->handlePrivmsg('foo', new IrcChannel('#bar'), '!amazon  ');

        $this->assertNull($response);
    }

    public function testAmazonSearch()
    {
        $responder = new ProductResponder();
        $response = $responder->handlePrivmsg('foo', new IrcChannel('#bar'), '!amazon Terraforming Mars');

        $this->assertNotNull($response);
        $this->assertContains('amazon.de', $response->getMessage());
        $this->assertContains('&field-keywords=Terraforming+Mars', $response->getMessage());
    }

    public function testBolSearch()
    {
        $responder = new ProductResponder();
        $response = $responder->handlePrivmsg('foo', new IrcChannel('#bar'), '!bol Terraforming Mars');

        $this->assertNotNull($response);
        $this->assertContains('bol.com', $response->getMessage());
        $this->assertContains('/zoekresultaten/Ntt/Terraforming+Mars', $response->getMessage());
    }
}
