<?php

namespace Tests\Fakes;

use App\Discord\MessageHandler;

class FakeMessage implements MessageHandler
{
    /**
     * @var bool
     */
    public $isDeleted = false;

    /**
     * @var string
     */
    public $replyText = '';

    /**
     * @var string
     */
    public $dmText = '';

    /**
     * @var string
     */
    private $content;

    public function __construct(string $content = '')
    {
        $this->content = $content;
    }

    public function delete()
    {
        $this->isDeleted = true;

        return $this;
    }

    public function reply(string $message)
    {
        $this->replyText = $message;

        return $this;
    }

    public function sendDm(string $message)
    {
        $this->dmText = $message;

        return $this;
    }

    public function getAuthor()
    {
        return new FakeUser();
    }

    public function getContent()
    {
        return $this->content;
    }
}