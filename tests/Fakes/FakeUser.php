<?php

namespace Tests\Fakes;

class FakeUser
{
    /**
     * @var string
     */
    public $id = '123456789';

    /**
     * @var string
     */
    public $username = 'random123';

    /**
     * @var string
     */
    public $messageText = '';

    /**
     * @var FakeUser
     */
    public $user;

    public function __construct()
    {
        $this->user = $this;
    }

    public function sendMessage(string $message)
    {
        $this->messageText = $message;
    }
}