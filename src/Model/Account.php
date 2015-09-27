<?php

namespace UserBase\Client\Model;

class Account
{
    private $name;
    private $displayName;
    private $about;
    private $pictureUrl;
    private $email;
    private $createdAt;
    private $deletedAt;
    private $accountType;
    
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
    
    
    
}
    
