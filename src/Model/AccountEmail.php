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

    public function setAccountName($accountName): static
    {
        $this->accountName = $accountName;

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

    public function getVerifiedAt()
    {
        return $this->verified_at;
    }

    public function setVerifiedAt($verified_at): static
    {
        $this->verified_at = $verified_at;

        return $this;
    }

    public function isPrimary()
    {
        return $this->primary;
    }

    public function setPrimary($primary): static
    {
        $this->primary = $primary;

        return $this;
    }
}
