<?php

namespace UserBase\Client\Model;

use InvalidArgumentException;

class Account
{
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

    public function __construct(private $name)
    {
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): static
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

    public function setDisplayName($displayName): static
    {
        $this->displayName = $displayName;

        return $this;
    }

    public function getAbout()
    {
        return $this->about;
    }

    public function setAbout($about): static
    {
        $this->about = $about;

        return $this;
    }

    public function getPictureUrl()
    {
        return $this->pictureUrl;
    }

    public function setPictureUrl($pictureUrl): static
    {
        $this->pictureUrl = $pictureUrl;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getEmailVerified()
    {
        return $this->email_verified;
    }

    public function setEmailVerified($email_verified): static
    {
        $this->email_verified = $email_verified;

        return $this;
    }

    public function getMobile()
    {
        return $this->mobile;
    }

    public function setMobile($mobile): static
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getMobileVerified()
    {
        return $this->mobile_verified;
    }

    public function setMobileVerified($mobile_verified): static
    {
        $this->mobile_verified = $mobile_verified;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status): static
    {
        $this->status = $status;

        return $this;
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

    public function getAccountType()
    {
        return $this->accountType;
    }

    public function setAccountType($accountType): static
    {
        $this->accountType = $accountType;

        return $this;
    }

    private array $accountUsers = [];

    public function addAccountUser(AccountUser $accountUser): static
    {
        $this->accountUsers[] = $accountUser;

        return $this;
    }

    public function getAccountUsers()
    {
        return $this->accountUsers;
    }

    public function isAccountUser($userName): bool
    {
        foreach ($this->accountUsers as $accountUser) {
            if ($accountUser === $userName) {
                return true;
            }
        }

        return false;
    }

    private array $accountEmails = [];

    public function addAccountEmail(AccountEmail $accountEmail): static
    {
        $this->accountEmails[] = $accountEmail;

        return $this;
    }

    public function getAccountEmails()
    {
        return $this->accountEmails;
    }

    public function hasVerifiedEmailDomain($emailDomain): bool
    {
        if ('@' != $emailDomain[0]) {
            throw new InvalidArgumentException('Make sure the domain argument starts with @');
        }
        foreach ($this->accountEmails as $accountEmail) {
            if ($accountEmail->getVerifiedAt() > 0) {
                if (str_ends_with((string) $accountEmail->getEmail(), (string) $emailDomain)) {
                    return true;
                }
            }
        }

        return false;
    }

    private array $accountProperties = [];

    public function addAccountProperty(AccountProperty $accountProperty): static
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

    public function hasAccountProperty($name): bool
    {
        return isset($this->accountProperties[$name]);
    }

    private $message;
    private $expireAt;

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getExpireAt()
    {
        return $this->expireAt;
    }

    public function setExpireAt($expireAt): static
    {
        $this->expireAt = $expireAt;

        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url): static
    {
        $this->url = $url;

        return $this;
    }
}
