<?php

namespace Tests\ResponderTests;

use App\Irc\Responders\FactResponder;
use App\Repositories\ChannelRepository;
use App\Repositories\FactRepository;
use App\Repositories\UserRepository;
use Jerodev\PhpIrcClient\IrcChannel;
use Tests\TestCase;

class FactResponderTest extends TestCase
{
    public function testFact()
    {
        $responder = $this->mockFactResponder([
            'getResponseString' => [
                ['foo', '#foo', true],
                ['smile', '#foo', true],
                ['👍', '#foo', true],
            ]
        ]);

        $channel = new IrcChannel('#foo');
        $responder->handlePrivmsg('jerodev', $channel, '!foo');             // Correct 0
        $responder->handlePrivmsg('jerodev', $channel, ' !foo');
        $responder->handlePrivmsg('jerodev', $channel, '!smile test test'); // Correct 1
        $responder->handlePrivmsg('jerodev', $channel, '!👍');              // Correct 2
        $responder->handlePrivmsg('jerodev', $channel, '! 👍');
    }

    public function testFactStats()
    {
        $responder = $this->mockFactResponder([
            'getSingleStats' => [
                ['foo', '#foo'],
                ['!foo', '#foo'],
                ['👍', '#foo'],
            ]
        ]);

        $channel = new IrcChannel('#foo');
        $responder->handlePrivmsg('jerodev', $channel, '!fact foo');                // Correct 0
        $responder->handlePrivmsg('jerodev', $channel, '!fact !foo');               // Correct 1
        $responder->handlePrivmsg('jerodev', $channel, '!fact smile test test');
        $responder->handlePrivmsg('jerodev', $channel, '!fact 👍');                 // Correct 2
        $responder->handlePrivmsg('jerodev', $channel, '! 👍');
    }

    public function testLearnFact()
    {
        $responder = $this->mockFactResponder([
            'learnFact' => [
                ['jerodev', '#foo', 'foo', 'bar'],
                ['jerodev', '#foo', 'smile', '😄'],
                ['jerodev', '#foo', '👍', '👎'],
            ]
        ]);

        $channel = new IrcChannel('#foo');
        $responder->handlePrivmsg('jerodev', $channel, 'Pokedex: !foo bar');          // Correct 0
        $responder->handlePrivmsg('jerodev', $channel, 'Pokedexx: !foo bar');
        $responder->handlePrivmsg('jerodev', $channel, 'Pokedex: foo bar');
        $responder->handlePrivmsg('jerodev', $channel, 'Pokedex: !foo bar', false);
        $responder->handlePrivmsg('jerodev', $channel, 'Pokedex: !smile 😄');         // Correct 1
        $responder->handlePrivmsg('jerodev', $channel, 'Pokedex: !👍 👎');            // Correct 2
    }

    public function testStats()
    {
        $responder = $this->mockFactResponder([
            'getStats' => [
                ['#foo'],
            ]
        ]);

        $channel = new IrcChannel('#foo');
        $responder->handlePrivmsg('jerodev', $channel, '!facts');             // Correct 0
        $responder->handlePrivmsg('jerodev', $channel, '!facts ');
        $responder->handlePrivmsg('jerodev', $channel, '!facts #foo');
    }

    public function testUndo()
    {
        $responder = $this->mockFactResponder([
            'getLastUserFact' => [
                ['jerodev', '#foo', 30],
            ]
        ]);

        $channel = new IrcChannel('#foo');
        $responder->handlePrivmsg('jerodev', $channel, '!undo');    // Correct 0
        $responder->handlePrivmsg('jerodev', $channel, '!undo test');
        $responder->handlePrivmsg('jerodev', $channel, '!undo ');
    }

    private function mockFactResponder(array $expected): FactResponder
    {
        $repository = $this->getMockBuilder(FactRepository::class)
            ->setConstructorArgs([app(ChannelRepository::class), app(UserRepository::class)])
            ->setMethods(array_keys($expected))
            ->getMock();
        foreach ($expected as $method => $values) {
            $repository->expects($this->exactly(count($values)))
                ->method($method)
                ->withConsecutive(...$values);
        }

        return new FactResponder($repository);
    }
}
