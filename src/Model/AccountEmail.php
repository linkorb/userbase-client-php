<?php

namespace UserBase\Client\Model;

class AccountEmail
{
    private $accountName;
    private $email;
    private $verified_at;
    private $primary;

    public function getAccountName()
    {
        return $this->accountName;
    }

    public function setAccountName($accountName)
    {
        $this->accountName = $accountName;

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

    public function getVerifiedAt()
    {
        return $this->verified_at;
    }

    public function setVerifiedAt($verified_at)
    {
        $this->verified_at = $verified_at;

        return $this;
    }

    public function isPrimary()
    {
        return $this->primary;
    }

    public function setPrimary($primary)
    {
        $this->primary = $primary;

        return $this;
    }
}
