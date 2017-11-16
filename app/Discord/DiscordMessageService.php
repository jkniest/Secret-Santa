<?php

namespace App\Discord;

use RestCord\DiscordClient;
use RestCord\Model\Channel\DmChannel;
use RestCord\Model\Channel\Message;

/**
 * This implementation of the message service will handle discord requests.
 *
 * @category Core
 * @package  SecretSanta
 * @author   Jordan Kniest <contact@jkniest.de>
 * @license  MIT <opensource.org/licenses/MIT>
 * @link     https://jkniest.de
 */
class DiscordMessageService implements MessageService
{
    /**
     * Delete a specific message in a specific channel.
     *
     * @param string $id        The id of the message which should be deleted.
     * @param string $channelId The id of the channel where the message lives.
     *
     * @return $this
     */
    public function delete(string $id, string $channelId)
    {
        $this->getClient()->channel->deleteMessage([
            'channel.id' => intval($channelId),
            'message.id' => intval($id)
        ]);

        return $this;
    }

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
    public function send(string $channelId, string $message, callable $callable = null)
    {
        /** @var Message $result */
        $result = $this->getClient()->channel->createMessage([
            'channel.id' => intval($channelId),
            'content'    => $message
        ]);

        if ($callable) {
            $array = (array) $result;
            $callable(new JsonMessage($array));
        }

        return $this;
    }

    /**
     * Send a Direct Message to a given user.
     *
     * @param string $userId  The id of the user.
     * @param string $message The message which should be send.
     *
     * @return $this
     */
    public function sendDm(string $userId, string $message)
    {
        /** @var DmChannel $channelId */
        $channel = $this->getClient()->user->createDm([
            'recipient_id' => intval($userId)
        ]);

        $this->send($channel->id, $message);

        return $this;
    }

    /**
     * Create a discord client with the configured token.
     *
     * @return DiscordClient
     */
    private function getClient()
    {
        return new DiscordClient(['token' => config('services.discord.token')]);
    }
}
