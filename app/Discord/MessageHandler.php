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
}
