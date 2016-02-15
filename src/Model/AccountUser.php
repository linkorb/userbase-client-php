<?php

namespace UserBase\Client\Model;

class AccountUser
{
    private $accountName;
    private $userName;
    private $isOwner = false;
    
    public function getAccountName()
    {
        return $this->accountName;
    }
    
    public function setAccountName($accountName)
    {
        $this->accountName = $accountName;
        return $this;
    }
    
    public function getUserName()
    {
        return $this->userName;
    }
    
    public function setUserName($userName)
    {
        $this->userName = $userName;
        return $this;
    }
    
    public function isOwner()
    {
        return ($this->isOwner==true);
    }
    
    public function setIsOwner($isOwner)
    {
        if ($isOwner) {
            $this->isOwner = true;
        } else {
            $this->isOwner = false;
        }
        return $this;
    }
    
    private $account;
    
    public function getAccount()
    {
        return $this->account;
    }
    
    public function setAccount(Account $account)
    {
        $this->account = $account;
        return $this;
    }
    
    private $user;
    
    public function getUser()
    {
        return $this->user;
    }
    
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }
}
