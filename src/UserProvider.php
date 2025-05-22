<?php

namespace UserBase\Client;

use LinkORB\Contracts\UserbaseRole\RoleManagerInterface;
use LinkORB\Contracts\UserbaseRole\RoleProviderInterface;
use Psr\Cache\InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use UserBase\Client\Event\UserLoadedEvent;
use UserBase\Client\Model\User;

class UserProvider implements UserProviderInterface, RoleManagerInterface
{
    private $roleProvider;

    public function __construct(
        private readonly Client $client,
        private readonly bool $shouldRefresh = true,
        private readonly EventDispatcherInterface|null $dispatcher = null
    ) {
    }

    public function setRoleProvider(RoleProviderInterface $roleProvider): void
    {
        $this->roleProvider = $roleProvider;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function loadUserByUsername($username): UserInterface
    {
        return $this->loadUserByIdentifier((string) $username);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        try {
            $user = $this->client->getUserByUsername($identifier);
            if ($this->dispatcher) {
                $event = new UserLoadedEvent($user);
                $this->dispatcher->dispatch($event, 'userbase.user_loaded');
            }
        } catch (RuntimeException $e) {
            throw new UserNotFoundException(
                "A User \"{$identifier}\" cannot be found in Userbase.",
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

    /**
     * @throws InvalidArgumentException
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        if (!$this->shouldRefresh) {
            return $user;
        }

        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass($class): bool
    {
        return User::class === $class;
    }
}
