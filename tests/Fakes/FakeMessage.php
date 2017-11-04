<?php

namespace Tests\Fakes;

class FakeMessage
{
    /**
     * @var FakeUser
     */
    public $author;

    /**
     * @var FakeChannel
     */
    public $channel;

    /**
     * @var bool
     */
    public $isDeleted = false;

    /**
     * @var string
     */
    public $replyText = '';

    public function __construct()
    {
        $this->author = new FakeUser();
        $this->channel = new FakeChannel();
    }

    public function reply(string $text)
    {
        $this->replyText = $text;
    }
}