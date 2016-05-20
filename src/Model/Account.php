<?php

namespace UserBase\Client\Model;

class Account
{
    private $name;
    private $displayName;
    private $about;
    private $pictureUrl;
    private $email;
    private $mobile;
    private $createdAt;
    private $deletedAt;
    private $accountType;
    
    public function __construct($name)
    {
        $this->name = $name;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    public function getDisplayName()
    {
        return $this->displayName;
    }
    
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
        return $this;
    }
    
    public function getAbout()
    {
        return $this->about;
    }
    
    public function setAbout($about)
    {
        $this->about = $about;
        return $this;
    }
    
    
    public function getPictureUrl()
    {
        return $this->pictureUrl;
    }
    
    public function setPictureUrl($pictureUrl)
    {
        $this->pictureUrl = $pictureUrl;
        return $this;
    }
    
    public function getEmail()
    {
        return $this->email;
    }
    
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }
    
    public function getMobile()
    {
        return $this->mobile;
    }
    
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
        return $this;
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
    
    public function getAccountType()
    {
        return $this->accountType;
    }
    
    public function setAccountType($accountType)
    {
        $this->accountType = $accountType;
        return $this;
    }
    
    private $accountUsers = array();
    
    public function addAccountUser(AccountUser $accountUser)
    {
        $this->accountUsers[] = $accountUser;
        return $this;
    }
    
    public function getAccountUsers()
    {
        return $this->accountUsers;
    }
    
    public function isAccountUser($userName)
    {
        foreach ($this->accountUser as $accountUser) {
            if ($this->accountUser->getUserName()==$userName) {
                return true;
            }
        }
        return false;
    }
    
    private $accountProperties = array();
    
    public function addAccountProperty(AccountProperty $accountProperty)
    {
        $this->accountProperties[$accountProperty->getName()] = $accountProperty;
        return $this;
    }
    
    public function getAccountProperties()
    {
        return $this->accountProperties;
    }
    
    public function getAccountProperty($name)
    {
        return $this->accountProperties[$name];
    }
    
    public function hasAccountProperty($name)
    {
        return isset($this->accountProperties[$name]);
    }

    private $message;
    private $expireAt;
    
    
    public function getMessage()
    {
        return $this->message;
    }
    
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }
    
    public function getExpireAt()
    {
        return $this->expireAt;
    }
    
    public function setExpireAt($expireAt)
    {
        $this->expireAt = $expireAt;
        return $this;
    }
}
