<?php

namespace App\Discord;

/**
 * The discord implementaion of a user.
 *
 * @category Core
 * @package  SecretSanta
 * @author   Jordan Kniest <contact@jkniest.de>
 * @license  MIT <opensource.org/licenses/MIT>
 * @link     https://jkniest.de
 */
class DiscordUser implements User
{
    /**
     * @var \Discord\Parts\User\User
     */
    private $original;

    /**
     * DiscordUser constructor.
     *
     * @param \Discord\Parts\User\User $original The original discord user object
     */
    public function __construct(\Discord\Parts\User\User $original)
    {
        $this->original = $original;
    }

    /**
     * Get the user id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->original->id;
    }

    /**
     * Get the username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->original->username;
    }
}
