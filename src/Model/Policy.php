<?php

namespace UserBase\Client\Model;

class Policy
{
    private $effect;
    private $actions = array();
    private $resource;

    public function getEffect()
    {
        return $this->effect;
    }

    public function setEffect($effect)
    {
        $this->effect = $effect;

        return $this;
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    public function addAction($action)
    {
        return $this->actions[$action] = true;
    }

    public function getActions()
    {
        return $this->actions;
    }
}
