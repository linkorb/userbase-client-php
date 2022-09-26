<?php

namespace UserBase\Client;

use LinkORB\Contracts\UserbaseRole\RoleManagerInterface;
use LinkORB\Contracts\UserbaseRole\RoleProviderInterface;
use RuntimeException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use UserBase\Client\Event\UserLoadedEvent;
use UserBase\Client\Model\User;

class UserProvider implements UserProviderInterface, RoleManagerInterface
{
    private $client;
    private $roleProvider;
    private $shouldRefresh;
    private $dispatcher;

    public function __construct(Client $client, $shouldRefresh = true, EventDispatcherInterface $dispatcher = null)
    {
        $this->client = $client;
        $this->shouldRefresh = (bool) $shouldRefresh;
        $this->dispatcher = $dispatcher;
    }

    public function setRoleProvider(RoleProviderInterface $roleProvider)
    {
        $this->roleProvider = $roleProvider;
    }

    public function loadUserByUsername($username)
    {
        try {
            $user = $this->client->getUserByUsername($username);
            if ($this->dispatcher) {
                $event = new UserLoadedEvent($user);
                $this->dispatcher->dispatch($event, 'userbase.user_loaded');
            }
        } catch (RuntimeException $e) {
            throw new UsernameNotFoundException(
                "A User named \"{$username}\" cannot be found in Userbase.",
                0,
                $e
            );
        }

        if (!$this->roleProvider) {
            return $user;
        }

        foreach ($this->roleProvider->getRoles($user) as $roleName) {
            $user->addRole($roleName);
        }

        return $user;
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
        return User::class === $class;
    }
}
