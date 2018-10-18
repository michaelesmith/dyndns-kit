<?php

namespace DynDNSKit;

use DynDNSKit\Handler\HandlerException;
use DynDNSKit\Handler\HandlerInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;

class Server
{
    /**
     * @var HandlerInterface[];
     */
    private $handlers;

    /**
     * @param HandlerInterface[] $handlers
     */
    public function __construct(array $handlers = [])
    {
        $this->handlers = $handlers;
    }

    /**
     * @param HandlerInterface $handler
     * @return $this
     */
    public function addHandler(HandlerInterface $handler): self
    {
        $this->handlers[] = $handler;

        return $this;
    }

    /**
     * @param Request $request
     * @throws HandlerException|RuntimeException
     */
    public function execute(Request $request)
    {
        $this->doExecute($request);
    }

    /**
     * @param Request $request
     * @throws HandlerException|RuntimeException
     */
    private function doExecute(Request $request)
    {
        foreach ($this->handlers as $handler) {
            if (HandlerInterface::SUCCESS === $handler->handle($request)) {

                return;
            }
        }

        throw new RuntimeException('No handler could be found to handle the current request');
    }
}
