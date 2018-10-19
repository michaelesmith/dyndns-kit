<?php
declare(strict_types = 1);

namespace DynDNSKit;

class Query
{
    /**
     * @var string
     */
    private $ip;

    /**
     * @var string[]
     */
    private $hostnames;

    /**
     * @param string $ip
     * @param string[] $hostnames
     */
    public function __construct(string $ip, array $hostnames)
    {
        $this->ip = $ip;
        $this->hostnames = $hostnames;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getHostnames(): array
    {
        return $this->hostnames;
    }
}
