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
    public $staticReplyText = '';

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

    public function staticReply(string $message, callable $callable = null)
    {
        $this->staticReplyText = $message;
        if ($callable) {
            $callable(new FakeMessage());
        }
    }

    public function getId()
    {
        return '1234';
    }

    public function getChannelId()
    {
        return '6789';
    }
}