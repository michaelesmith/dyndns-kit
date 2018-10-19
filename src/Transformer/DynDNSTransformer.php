<?php
declare(strict_types = 1);

namespace DynDNSKit\Transformer;

use DynDNSKit\Query;
use Symfony\Component\HttpFoundation\Request;

class DynDNSTransformer implements TransformerInterface
{
    const QUERY_HOSTNAME = 'hostname';

    const QUERY_IP = 'myip';

    const PATH_INFO = ['/v3/update', '/nic/update'];

    /**
     * @inheritdoc
     */
    public function transform(Request $request): ?Query
    {
        // make sure the request appears to be a dyndns request
        if (!$request->isMethod(Request::METHOD_GET)) {
            throw new TransformerFailedException(
                sprintf('The request should have a method of GET but %s was given', $request->getMethod()),
                TransformerFailedException::REQUEST_METHOD
            );
        }
        if (!$request->query->has(self::QUERY_HOSTNAME)) {
            throw new TransformerFailedException(
                sprintf('The request should contain a %s parameter', self::QUERY_HOSTNAME),
                TransformerFailedException::REQUEST_PARAMETER
            );
        }
        if (!in_array($request->getPathInfo(), self::PATH_INFO)) {
            throw new TransformerFailedException(
                sprintf('The request should contain a path of one of the following %s', implode(', ', self::PATH_INFO)),
                TransformerFailedException::REQUEST_PATH
            );
        }

        return new Query(
            $request->query->has(self::QUERY_IP) ? $request->query->get(self::QUERY_IP) : $request->getClientIp(),
            explode(',', $request->query->get(self::QUERY_HOSTNAME))
        );
    }
}
