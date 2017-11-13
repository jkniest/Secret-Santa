<?php

namespace App\Discord;

use Zttp\PendingZttpRequest;

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
        $this->makeRequest('delete', "channels/{$channelId}/messages/{$id}");

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
        $result = $this->makeRequest('post', "channels/{$channelId}/messages", [
            'content' => $message
        ]);

        if ($callable) {
            $callable(new JsonMessage($result));
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
        $dmChannelId = $this->makeRequest('post', 'users/@me/channels', [
            'recipient_id' => $userId
        ])['id'];

        $this->send($dmChannelId, $message);

        return $this;
    }

    /**
     * Make a request to the discord servers.
     *
     * @param string $method      The http method which should be used in lower-case.
     * @param string $relativeUrl The relative url to their api without trailing slash
     * @param array  $params      Parameters which should be send
     *
     * @return array
     */
    private function makeRequest(string $method, string $relativeUrl, array $params = [])
    {
        return PendingZttpRequest::new()->withHeaders([
            'Authorization' => 'Bot ' . config('services.discord.token')
        ])->{$method}($this->getApiUrl($relativeUrl), $params)->json();
    }

    /**
     * Get the full API url for a specific resource.
     *
     * @param string $relative The relative url whitch should be added to the api url.
     *
     * @return string
     */
    private function getApiUrl(string $relative)
    {
        return "https://discordapp.com/api/v6/{$relative}";
    }
}
