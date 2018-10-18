<?php

namespace DynDNSKit\Authenticator\User;

interface UserInterface
{
    /**
     * @return string
     */
    public function getUsername(): string;

    /**
     * @param string $password
     * @return bool
     */
    public function checkPassword(string $password): bool;

    /**
     * @param $hostname
     * @return bool
     */
    public function authorizeHostname(string $hostname): bool;

    /**
     * @param $hostnames
     * @return bool
     */
    public function authorizeHostnames(array $hostnames): bool;
}
