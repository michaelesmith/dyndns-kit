<?php

namespace DynDNSKit\Tests\Unit\Handler;

use DynDNSKit\Authenticator\AuthenticatorException;
use DynDNSKit\Authenticator\AuthenticatorInterface;
use DynDNSKit\Handler\GenericHandler;
use DynDNSKit\Handler\HandlerException;
use DynDNSKit\Handler\HandlerInterface;
use DynDNSKit\Processor\ProcessorException;
use DynDNSKit\Processor\ProcessorInterface;
use DynDNSKit\Query;
use DynDNSKit\Tests\Common\TestCase;
use DynDNSKit\Transformer\TransformerException;
use DynDNSKit\Transformer\TransformerFailedException;
use DynDNSKit\Transformer\TransformerInterface;
use Symfony\Component\HttpFoundation\Request;

class GenericHandlerTest extends TestCase
{
    public function dpTestHandle()
    {
        return [
            0 => [
                HandlerInterface::SUCCESS, // $expected
                $this->mockTransformer(), // $transformer
                $this->mockAuthenticator(), // $authenticator
                $this->mockProcessor(), // $processor
            ],
            1 => [
                HandlerInterface::DEFERRED, // $expected
                $this->mockTransformer(new TransformerFailedException()), // $transformer
                $this->mockAuthenticator(), // $authenticator
                $this->mockProcessor(), // $processor
                'The transformer is unable to transform this request', // $message
            ],
            2 => [
                null, // $expected
                $this->mockTransformer(), // $transformer
                $this->mockAuthenticator(true), // $authenticator
                $this->mockProcessor(), // $processor
                'The authentication failed', // $message
                true, // $expectException
            ],
            3 => [
                null, // $expected
                $this->mockTransformer(), // $transformer
                $this->mockAuthenticator(), // $authenticator
                $this->mockProcessor(true), // $processor
                'The processor failed', // $message
                true, // $expectException
            ],
        ];
    }

    private function mockTransformer(TransformerException $exception = null)
    {
        $mock = \Mockery::mock(TransformerInterface::class);

        if ($exception) {
            $mock->shouldReceive('transform')->andThrow($exception);
        } else {
            $mock->shouldReceive('transform')->andReturn(new Query('127.0.0.1', ['myhost']));
        }

        return $mock;
    }

    private function mockAuthenticator($exception = false)
    {
        $mock = \Mockery::mock(AuthenticatorInterface::class);
        if ($exception) {
            $mock->shouldReceive('authenticate')->andThrows(new AuthenticatorException());
        } else {
            $mock->shouldReceive('authenticate')->andReturn(true);
        }

        return $mock;
    }

    private function mockProcessor($exception = false)
    {
        $mock = \Mockery::mock(ProcessorInterface::class);
        if ($exception) {
            $mock->shouldReceive('process')->andThrow(new ProcessorException());
        } else {
            $mock->shouldReceive('process')->andReturn(true);
        }

        return $mock;
    }

    /**
     * @dataProvider dpTestHandle
     */
    public function testHandle($expected, $transformer, $authenticator, $processor, $message = '', $expectException = false)
    {
        $sut = new GenericHandler($transformer, $authenticator, $processor);

        if ($expectException) {
            $this->expectException(HandlerException::class);
        }

        $this->assertEquals($expected, $sut->handle(Request::create('/')), $message);
    }
}
