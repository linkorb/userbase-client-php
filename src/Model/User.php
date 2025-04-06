<?php

namespace UserBase\Client\Model;

use LinkORB\Contracts\UserbaseRole\RoleInterface;
use RuntimeException;
use Symfony\Component\Security\Core\User\LegacyPasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;

final class User implements
    AccountContainerInterface,
    BaseUserInterface,
    LegacyAdvancedUserInterface,
    LegacyPasswordAuthenticatedUserInterface,
    PolicyContainerInterface,
    RoleInterface,
    UserInterface
{
    /**
     * @deprecated
     */
    private bool $enabled;
    /**
     * @deprecated
     */
    private bool $accountNonExpired;
    /**
     * @deprecated
     */
    private bool $credentialsNonExpired;
    /**
     * @deprecated
     */
    private bool $accountNonLocked;

    private $name;
    private string $password = '';
    private array $roles = [];
    private string $salt = '';

    private $createdAt;
    private $lastSeenAt;
    private $deletedAt;

    private array $accountUsers = [];
    private array $policies = [];

    public function __construct(string $name)
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('The name cannot be empty.');
        }

        $this->name = $name;
        $this->enabled = true;
        $this->accountNonExpired = true;
        $this->credentialsNonExpired = true;
        $this->accountNonLocked = true;
        $this->salt = 'KJH6212kjwek_fj23D01-239.1023fkjdsj^k2hdfssfjk!h234uiy4324';
    }

    public function getUserIdentifier(): string
    {
        return $this->name;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    public function setDeletedAt($deletedAt): static
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    public function getLastSeenAt()
    {
        return $this->lastSeenAt;
    }

    public function setLastSeenAt($lastSeenAt): static
    {
        if ($this->lastSeenAt > 0) {
            $this->lastSeenAt = $lastSeenAt;
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt(): ?string
    {
        return $this->salt;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername(): string
    {
        return $this->getName();
    }

    public function setUsername(string $username): static
    {
        $this->name = $username;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDisplayName()
    {
        $account = $this->getUserAccount();
        return $account->getDisplayName();
    }

    /**
     * @deprecated
     */
    public function isAccountNonExpired(): bool
    {
        return $this->accountNonExpired;
    }

    /**
     * @deprecated
     */
    public function isAccountNonLocked(): bool
    {
        return $this->accountNonLocked;
    }

    /**
     * @deprecated
     */
    public function setAccountNonLocked($accountNonLocked): static
    {
        $this->accountNonLocked = $accountNonLocked;

        return $this;
    }

    /**
     * @deprecated
     */
    public function isCredentialsNonExpired(): bool
    {
        return $this->credentialsNonExpired;
    }

    /**
     * @deprecated
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @deprecated
     */
    public function setEnabled($enabled): static
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }

    public function getEmail()
    {
        return $this->getUserAccount()->getEmail();
    }

    public function getPictureUrl($size = null)
    {
        return $this->getUserAccount()->getPictureUrl($size);
    }

    public function addAccountUser(AccountUser $accountUser): void
    {
        $this->accountUsers[] = $accountUser;
    }

    public function getAccountUsers(): array
    {
        return $this->accountUsers;
    }

    public function getAccounts(): array
    {
        $accounts = array();
        foreach ($this->accountUsers as $accountUser) {
            $accounts[] = $accountUser->getAccount();
        }
        return $accounts;
    }

    public function getUserAccount()
    {
        foreach ($this->getAccounts() as $account) {
            if ($account->getName() == $this->getName()) {
                return $account;
            }
        }
        throw new RuntimeException('This user has no user-account: '.$this->getName());
    }

    public function getAccountsByType($type): array
    {
        $res = array();
        foreach ($this->getAccounts() as $account) {
            if ($account->getAccountType() == $type) {
                $res[] = $account;
            }
        }
        return $res;
    }

    public function addPolicy(Policy $policy): void
    {
        $this->policies[] = $policy;
    }

    public function getPolicies(): array
    {
        return $this->policies;
    }

    public function addRole($role): void
    {
        $this->roles[] = $role;
    }
}
