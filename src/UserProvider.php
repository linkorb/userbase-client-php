<?php

namespace UserBase\Client;

use RuntimeException;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

use UserBase\Client\Model\User;

class UserProvider implements UserProviderInterface
{
    private $client;
    private $shouldRefresh;

    public function __construct(Client $client, $shouldRefresh = true)
    {
        $this->client = $client;
        $this->shouldRefresh = (bool) $shouldRefresh;
    }

    public function loadUserByUsername($username)
    {
        try {
            return $this->client->getUserByUsername($username);
        } catch (RuntimeException $e) {
            throw new UsernameNotFoundException(
                "A User named \"{$username}\" cannot be found in Userbase.",
                null,
                $e
            );
        }
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        if (!$this->shouldRefresh) {
            return $user;
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === User::class;
    }
}
