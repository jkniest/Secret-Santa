<?php

namespace App\Discord;

/**
 * This implementation will transform a json object into a message.
 *
 * @category Core
 * @package  SecretSanta
 * @author   Jordan Kniest <contact@jkniest.de>
 * @license  MIT <opensource.org/licenses/MIT>
 * @link     https://jkniest.de
 */
class JsonMessage implements MessageHandler
{
    /**
     * @var array
     */
    private $original;

    /**
     * JsonMessage constructor.
     *
     * @param array $original The original json data
     */
    public function __construct(array $original)
    {
        $this->original = $original;
    }

    /**
     * Delete the message.
     *
     * @return $this
     */
    public function delete()
    {
        app(MessageService::class)->delete(
            $this->original['id'], $this->original['channel_id']
        );

        return $this;
    }

    /**
     * Reply to the message.
     *
     * @param string $message The message which should be send.
     *
     * @return $this
     */
    public function reply(string $message)
    {
        app(MessageService::class)->send(
            $this->original['channel_id'],
            "<@{$this->original['author']['id']}>, {$message}"
        );

        return $this;
    }

    /**
     * Post a static reply to the channel of the message. The difference between
     * 'reply' and 'staticReply' is, that staticReply does not mention the author
     * of the message.
     *
     * @param string        $message  The message which should be send
     * @param callable|null $callable A optional callable that will receive the sent message
     *
     * @return $this
     */
    public function staticReply(string $message, callable $callable = null)
    {
        app(MessageService::class)->send(
            $this->original['channel_id'],
            $message
        );

        return $this;
    }

    /**
     * Send a DM to the author of the message.
     *
     * @param string $message The message which should be send.
     *
     * @return $this
     */
    public function sendDm(string $message)
    {
        app(MessageService::class)->sendDm(
            $this->original['author']['id'],
            $message
        );

        return $this;
    }

    /**
     * Get the author of the given message.
     *
     * @return User
     */
    public function getAuthor()
    {
        return new JsonUser($this->original['author']);
    }

    /**
     * Get the content of the message.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->original['content'];
    }

    /**
     * Get the id of the message.
     *
     * @return string
     */
    public function getId()
    {
        return $this->original['id'];
    }

    /**
     * Get the id of the channel.
     *
     * @return string
     */
    public function getChannelId()
    {
        return $this->original['channel_id'];
    }
}
