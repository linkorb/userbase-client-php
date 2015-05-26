<?php

namespace UserBase\Client;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use UserBase\Client\User;
use UserBase\Client\Client;
use RuntimeException;

final class UserProvider implements UserProviderInterface
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }
    
    public function loadUserByUsername($username)
    {
        $user = $this->client->getUserByUsername($username);
        if (!$user) {
            throw new UsernameNotFoundException(sprintf('User %s is not found.', $username));
        }
        return $user;
    }
    
    // Needed for symfony user provider interface
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    // Needed for symfony user provider interface

    public function supportsClass($class)
    {
        return $class === 'Symfony\Component\Security\Core\User\User';
    }
}
