<?php

namespace UserBase\Client\Model;

class Policy
{
    private $effect;
    private array $actions = [];
    private $resource;

    public function getEffect()
    {
        return $this->effect;
    }

    public function setEffect($effect): static
    {
        $this->effect = $effect;

        return $this;
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function setResource($resource): static
    {
        $this->resource = $resource;

        return $this;
    }

    public function addAction($action): true
    {
        return $this->actions[$action] = true;
    }

    public function getActions(): array
    {
        return $this->actions;
    }
}
