<?php
declare(strict_types = 1);

namespace DynDNSKit\Authenticator\User;

abstract class AbstractUser implements UserInterface
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @param string $username
     * @param string $password
     */
    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @inheritdoc
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @inheritdoc
     */
    public function checkPassword(string $password): bool
    {
        return $this->password === $password;
    }
}
