<?php

namespace UserBase\Client\Event;

use Symfony\Contracts\EventDispatcher\Event;
use UserBase\Client\Model\User;

class UserLoadedEvent extends Event
{
    public const NAME = 'userbase.user_loaded';

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
}
