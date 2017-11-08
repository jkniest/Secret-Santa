<?php

namespace App\Discord;

use Discord\Parts\Channel\Message;

/**
 * The discord implementation of a message.
 *
 * @category Core
 * @package  SecretSanta
 * @author   Jordan Kniest <contact@jkniest.de>
 * @license  MIT <opensource.org/licenses/MIT>
 * @link     https://jkniest.de
 */
class DiscordMessage implements MessageHandler
{
    /**
     * @var Message
     */
    private $original;

    /**
     * DiscordMessage constructor.
     *
     * @param Message $original The original discord message
     */
    public function __construct(Message $original)
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
        $this->original->channel->messages->delete($this->original);

        return $this;
    }

    /**
     * Reply to the message.
     *
     * @param string $message The message which should be replied.
     *
     * @return $this
     */
    public function reply(string $message)
    {
        $this->original->reply($message);

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
        $this->original->author->user->sendMessage($message);

        return $this;
    }

    /**
     * Get the author of the given message.
     *
     * @return DiscordUser
     */
    public function getAuthor()
    {
        return new DiscordUser($this->original->author->user);
    }

    /**
     * Get the content of the message.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->original->content;
    }

    /**
     * Get the id of the message.
     *
     * @return string
     */
    public function getId()
    {
        return $this->original->id;
    }

    /**
     * Get the id of the channel.
     *
     * @return string
     */
    public function getChannelId()
    {
        return $this->original->channel_id;
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
        $this->original->channel->sendMessage($message)->then(function ($message) use ($callable) {
            if ($callable) {
                $callable(new DiscordMessage($message));
            }
        });

        return $this;
    }
}
