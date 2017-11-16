<?php

namespace Tests\Fakes;

use App\Discord\MessageService;

class FakeMessageService implements MessageService
{
    /**
     * @var string
     */
    public $deletedPost = '';

    /**
     * @var string
     */
    public $deletedPostChannel = '';

    /**
     * @var string
     */
    public $channelId = '';

    /**
     * @var string
     */
    public $message = '';

    /**
     * @var array
     */
    public $dmMessages = [];

    public function delete(string $id, string $channelId)
    {
        $this->deletedPost = $id;
        $this->deletedPostChannel = $channelId;
    }

    public function send(string $channelId, string $message, callable $callable = null)
    {
        $this->channelId = $channelId;
        $this->message = $message;

        if ($callable) {
            $callable(new FakeMessage());
        }
    }

    public function sendDm(string $userId, string $message)
    {
        $this->dmMessages[] = $message;
    }
}