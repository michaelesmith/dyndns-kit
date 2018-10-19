<?php
declare(strict_types = 1);

namespace DynDNSKit\Handler;

use DynDNSKit\Authenticator\AuthenticatorInterface;
use DynDNSKit\Exception;
use DynDNSKit\Processor\ProcessorInterface;
use DynDNSKit\Transformer\TransformerFailedException;
use DynDNSKit\Transformer\TransformerInterface;
use Symfony\Component\HttpFoundation\Request;

class GenericHandler implements HandlerInterface
{
    /**
     * @var TransformerInterface
     */
    private $transformer;

    /**
     * @var AuthenticatorInterface
     */
    private $authenticator;

    /**
     * @var ProcessorInterface
     */
    private $processor;

    /**
     * @param TransformerInterface $transformer
     * @param AuthenticatorInterface $authenticator
     * @param ProcessorInterface $processor
     */
    public function __construct(TransformerInterface $transformer, AuthenticatorInterface $authenticator, ProcessorInterface $processor)
    {
        $this->transformer = $transformer;
        $this->authenticator = $authenticator;
        $this->processor = $processor;
    }

    /**
     * @inheritdoc
     */
    public function handle(Request $request): int
    {
        try {
            $query = $this->transformer->transform($request);
            $this->authenticator->authenticate($request, $query);
            $this->processor->process($query);
        } catch (TransformerFailedException $e) {
            // Our transformer can't convert the request to a query so let other handlers try

            return HandlerInterface::DEFERRED;
        } catch (Exception $e) {

            throw new HandlerException('An exception was thrown while trying to handle the request', 0, $e);
        }

        return HandlerInterface::SUCCESS;
    }
}
