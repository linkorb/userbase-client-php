<?php

namespace UserBase\Client\Model;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\Role\Role;
use RuntimeException;

final class User implements UserInterface, AdvancedUserInterface, AccountContainerInterface, PolicyContainerInterface
{
    private $password;
    private $enabled;
    private $accountNonExpired;
    private $credentialsNonExpired;
    private $accountNonLocked;
    private $roles;
    
    private $createdAt;
    private $lastSeenAt;
    private $deletedAt;
    
    private $accountUsers = array();
    private $policies = array();

    public function __construct($name)
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('The name cannot be empty.');
        }

        $this->name = $name;
        $this->password = null;
        $this->enabled = true;
        $this->accountNonExpired = true;
        $this->credentialsNonExpired = true;
        $this->accountNonLocked = true;
        $this->roles = array();
        $this->salt = "KJH6212kjwek_fj23D01-239.1023fkjdsj^k2hdfssfjk!h234uiy4324";
    }
    
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }
    
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }
    
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }
        
    public function getLastSeenAt()
    {
        return $this->lastSeenAt;
    }
    
    public function setLastSeenAt($lastSeenAt)
    {
        if ($this->lastSeenAt>0) {
            $this->lastSeenAt = $lastSeenAt;
        }
        return null;
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
    public function getPassword()
    {
        return $this->password;
    }
    
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
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
    public function setUsername($username)
    {
        $this->name = $username;
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
     * {@inheritdoc}
     */
    public function isAccountNonExpired()
    {
        return $this->accountNonExpired;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked()
    {
        return $this->accountNonLocked;
    }

    /**
     * {@inheritdoc}
     */
    public function isCredentialsNonExpired()
    {
        return $this->credentialsNonExpired;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->enabled;
    }
    
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
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
        throw new RuntimeException("This user has no user-account: " . $this->getName());
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
