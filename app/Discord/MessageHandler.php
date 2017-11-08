<?php

namespace App\Discord;

/**
 * Handler for all messages. It has some generic methods, like deleting a message or repling to
 * a message.
 *
 * @category Core
 * @package  SecretSanta
 * @author   Jordan Kniest <contact@jkniest.de>
 * @license  MIT <opensource.org/licenses/MIT>
 * @link     https://jkniest.de
 */
interface MessageHandler
{
    /**
     * Delete the message.
     *
     * @return $this
     */
    public function delete();

    /**
     * Reply to the message.
     *
     * @param string $message The message which should be send.
     *
     * @return $this
     */
    public function reply(string $message);

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
    public function staticReply(string $message, callable $callable = null);

    /**
     * Send a DM to the author of the message.
     *
     * @param string $message The message which should be send.
     *
     * @return $this
     */
    public function sendDm(string $message);

    /**
     * Get the author of the given message.
     *
     * @return User
     */
    public function getAuthor();

    /**
     * Get the content of the message.
     *
     * @return string
     */
    public function getContent();

    /**
     * Get the id of the message.
     *
     * @return string
     */
    public function getId();

    /**
     * Get the id of the channel.
     *
     * @return string
     */
    public function getChannelId();
}
