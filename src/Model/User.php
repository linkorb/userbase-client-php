<?php

namespace UserBase\Client\Model;

use LinkORB\Contracts\UserbaseRole\RoleInterface;
use RuntimeException;
use Symfony\Component\Security\Core\User\LegacyPasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;

final class User implements
    AccountContainerInterface,
    BaseUserInterface,
    LegacyAdvancedUserInterface,
    LegacyPasswordAuthenticatedUserInterface,
    PasswordAuthenticatedUserInterface,
    PolicyContainerInterface,
    RoleInterface,
    UserInterface
{
    /**
     * @deprecated
     */
    private $enabled;
    /**
     * @deprecated
     */
    private $accountNonExpired;
    /**
     * @deprecated
     */
    private $credentialsNonExpired;
    /**
     * @deprecated
     */
    private $accountNonLocked;

    private $name;
    private $password = '';
    private $roles;
    private $salt = '';

    private $createdAt;
    private $lastSeenAt;
    private $deletedAt;

    private $accountUsers = array();
    private $policies = array();

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
        $this->roles = array();
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

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    public function getLastSeenAt()
    {
        return $this->lastSeenAt;
    }

    public function setLastSeenAt($lastSeenAt)
    {
        if ($this->lastSeenAt > 0) {
            $this->lastSeenAt = $lastSeenAt;
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
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

    public function setPassword(string $password)
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
    public function getUsername()
    {
        return $this->getName();
    }

    public function setUsername(string $username)
    {
        $this->name = $username;
        return $this;
    }

    public function getName()
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
    public function isAccountNonExpired()
    {
        return $this->accountNonExpired;
    }

    /**
     * @deprecated
     */
    public function isAccountNonLocked()
    {
        return $this->accountNonLocked;
    }

    /**
     * @deprecated
     */
    public function setAccountNonLocked($accountNonLocked)
    {
        $this->accountNonLocked = $accountNonLocked;

        return $this;
    }

    /**
     * @deprecated
     */
    public function isCredentialsNonExpired()
    {
        return $this->credentialsNonExpired;
    }

    /**
     * @deprecated
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @deprecated
     */
    public function setEnabled($enabled)
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

    public function addAccountUser(AccountUser $accountUser)
    {
        $this->accountUsers[] = $accountUser;
    }

    public function getAccountUsers()
    {
        return $this->accountUsers;
    }

    public function getAccounts()
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

    public function getAccountsByType($type)
    {
        $res = array();
        foreach ($this->getAccounts() as $account) {
            if ($account->getAccountType() == $type) {
                $res[] = $account;
            }
        }
        return $res;
    }

    public function addPolicy(Policy $policy)
    {
        $this->policies[] = $policy;
    }

    public function getPolicies()
    {
        return $this->policies;
    }

    public function addRole($roleName)
    {
        $this->roles[] = $roleName;
    }
}
