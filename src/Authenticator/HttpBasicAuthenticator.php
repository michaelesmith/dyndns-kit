<?php
declare(strict_types = 1);

namespace DynDNSKit\Authenticator;

use DynDNSKit\Authenticator\User\UserInterface;
use DynDNSKit\Query;
use Symfony\Component\HttpFoundation\Request;

class HttpBasicAuthenticator implements AuthenticatorInterface
{
    /**
     * @var UserInterface[]
     */
    private $users;

    /**
     * @param UserInterface[] $users
     */
    public function __construct(array $users)
    {
        $this->users = $users;
    }

    /**
     * @inheritdoc
     */
    public function authenticate(Request $request, Query $query): bool
    {
        $user = $this->findUser($request->getUser());

        if (!$user) {
            throw new AuthenticatorException(
                sprintf('A user with the username "%s" could not be found', $request->getUser()),
                AuthenticatorException::USER_NOT_FOUND
            );
        }

        if (!$user->checkPassword($request->getPassword())) {
            throw new AuthenticatorException(
                sprintf('The password "%s" is incorrect', $request->getPassword()),
                AuthenticatorException::BAD_CREDENTIALS
            );
        }

        if (!$user->authorizeHostnames($query->getHostnames())) {
            throw new AuthenticatorException(
                sprintf('The user is not authorized for the hostnames: %s', implode(', ', $query->getHostnames())),
                AuthenticatorException::UNAUTHORIZED_HOSTNAME
            );
        }

        return true;
    }

    /**
     * @param string $username
     * @return UserInterface|null
     */
    private function findUser(string $username): ?UserInterface
    {
        foreach ($this->users as $user) {
            if ($user->getUsername() === $username) {

                return $user;
            }
        }

        return null;
    }
}
