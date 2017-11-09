<?php

namespace App\Discord;

/**
 * The message service interface does provide methods to interact with the chat software.
 * For example, deleting specific messages or sending new ones.
 *
 * @category Core
 * @package  SecretSanta
 * @author   Jordan Kniest <contact@jkniest.de>
 * @license  MIT <opensource.org/licenses/MIT>
 * @link     https://jkniest.de
 */
interface MessageService
{
    /**
     * Delete a specific message in a specific channel.
     *
     * @param string $id        The id of the message which should be deleted.
     * @param string $channelId The id of the channel where the message lives.
     *
     * @return $this
     */
    public function delete(string $id, string $channelId);

    /**
     * Send a new message in a specific channel.
     *
     * @param string        $channelId The id of the channel where the message should be send.
     * @param string        $message   The message which should be send.
     * @param callable|null $callable  A callable that is triggered with the server response
     *                                 message.
     *
     * @return $this
     */
    public function send(string $channelId, string $message, callable $callable = null);

    /**
     * Send a Direct Message to a given user.
     *
     * @param string $userId  The id of the user.
     * @param string $message The message which should be send.
     *
     * @return $this
     */
    public function sendDm(string $userId, string $message);
}
