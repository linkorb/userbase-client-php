<?php

namespace UserBase\Client\Model;

class AccountProperty
{
    private $accountName;
    private $name;
    private $value;

    public function getAccountName()
    {
        return $this->accountName;
    }

    public function setAccountName($accountName): static
    {
        $this->accountName = $accountName;

        return $this;
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

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value): static
    {
        $this->value = $value;

        return $this;
    }
}
