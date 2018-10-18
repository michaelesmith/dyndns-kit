<?php

namespace DynDNSKit\Tests\Unit;

use DynDNSKit\Handler\HandlerException;
use DynDNSKit\Handler\HandlerInterface;
use DynDNSKit\Server;
use DynDNSKit\Tests\Common\TestCase;
use Symfony\Component\HttpFoundation\Request;

class ServerTest extends TestCase
{
    public function dpTestExecute()
    {
        return [
            0 => [
                [
                    HandlerInterface::SUCCESS,
                ], // $handlers
                'Properly executes a single handler', // $message
            ],
            1 => [
                [
                    HandlerInterface::DEFERRED,
                    HandlerInterface::SUCCESS,
                ], // $handlers
                'Properly executes the second handler', // $message
            ],
            2 => [
                [
                    HandlerInterface::SUCCESS,
                    null,
                ], // $handlers
                'Properly executes only the first handler', // $message
            ],
            3 => [
                [
                    new HandlerException(),
                    null,
                ], // $handlers
                'Stops processing upon exception', // $message
                HandlerException::class,
            ],
            4 => [
                [
                    HandlerInterface::DEFERRED,
                    HandlerInterface::DEFERRED,
                ], // $handlers
                'Throws exception if no handler found', // $message
                \RuntimeException::class,
            ],
        ];
    }

    private function mockHandler($return = null)
    {
        $mock = \Mockery::mock(HandlerInterface::class);
        $method = $mock->shouldReceive('handle');
        if ($return) {
            $method = $method->once();
            $return instanceof \Exception ? $method->andThrow($return) : $method->andReturn($return);
        } else {
            $method->never();
        }

        return $mock;
    }

    /**
     * @dataProvider dpTestExecute
     */
    public function testExecute($handlers, $message = '', $exception = null)
    {
        $handlers = array_map(function ($return) { return $this->mockHandler($return); }, $handlers);
        $sut = new Server($handlers);

        if ($exception) {
            $this->expectException($exception);
        }

        // If we don't make any assertions the test is considered risky
        $this->assertNull($sut->execute(Request::create('/')));
    }
}
