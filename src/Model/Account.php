<?php

namespace UserBase\Client\Model;

use InvalidArgumentException;

class Account
{
    private $name;
    private $displayName;
    private $about;
    private $pictureUrl;
    private $email;
    private $email_verified;
    private $mobile;
    private $mobile_verified;
    private $status;
    
    private $createdAt;
    private $deletedAt;
    private $accountType;
    private $url;
    
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
        if ($this->displayName) {
            return $this->displayName;
        }
        return $this->name;
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
    
    public function getEmailVerified()
    {
        return $this->email_verified;
    }
    
    public function setEmailVerified($email_verified)
    {
        $this->email_verified = $email_verified;
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
    
    public function getMobileVerified()
    {
        return $this->mobile_verified;
    }
    
    public function setMobileVerified($mobile_verified)
    {
        $this->mobile_verified = $mobile_verified;
        return $this;
    }
    
    
    public function getStatus()
    {
        return $this->status;
    }
    
    public function setStatus($status)
    {
        $this->status = $status;
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


    private $accountEmails = array();
    
    public function addAccountEmail(AccountEmail $accountEmail)
    {
        $this->accountEmails[] = $accountEmail;
        return $this;
    }
    
    public function getAccountEmails()
    {
        return $this->accountEmails;
    }
    
    public function hasVerifiedEmailDomain($emailDomain)
    {
        if ($emailDomain[0]!='@') {
            throw new InvalidArgumentException('Make sure the domain argument starts with @');
        }
        foreach ($this->accountEmails as $accountEmail) {
            if ($accountEmail->getVerifiedAt()>0) {
                if (substr($accountEmail->getEmail(), -strlen($emailDomain))==$emailDomain) {
                    return true;
                }
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
    
    public function getUrl()
    {
        return $this->url;
    }
    
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }
}
