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

    public function setAccountName($accountName): static
    {
        $this->accountName = $accountName;

        return $this;
    }

    public function getUserName()
    {
        return $this->userName;
    }

    public function setUserName($userName): static
    {
        $this->userName = $userName;

        return $this;
    }

    public function isOwner(): bool
    {
        return true == $this->isOwner;
    }

    public function setIsOwner($isOwner): static
    {
        if ($isOwner) {
            $this->isOwner = true;
        } else {
            $this->isOwner = false;
        }

        return $this;
    }

    private Account $account;

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): static
    {
        $this->account = $account;

        return $this;
    }

    private $user;

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user): static
    {
        $this->user = $user;

        return $this;
    }
}
