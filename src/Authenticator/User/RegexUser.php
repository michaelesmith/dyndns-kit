<?php
declare(strict_types = 1);

namespace DynDNSKit\Authenticator\User;

class RegexUser extends AbstractUser
{
    /**
     * @var string
     */
    private $regex;

    /**
     * @param string $username
     * @param string $password
     * @param string $regex
     */
    public function __construct(string $username, string $password, string $regex)
    {
        parent::__construct($username, $password);
        $this->regex = $regex;
    }

    /**
     * @param string $hostname
     * @return bool
     */
    public function authorizeHostname(string $hostname): bool
    {
        return (bool) preg_match('/' . $this->regex . '/', $hostname);
    }

    /**
     * @param string[] $hostnames
     * @return bool
     */
    public function authorizeHostnames(array $hostnames): bool
    {
        foreach ($hostnames as $hostname) {
            if (!$this->authorizeHostname($hostname)) {
                return false;
            }
        }

        return true;
    }
}
