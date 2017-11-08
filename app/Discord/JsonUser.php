<?php

namespace App\Discord;

/**
 * This implemention will format a json object to a user.
 *
 * @category Core
 * @package  SecretSanta
 * @author   Jordan Kniest <contact@jkniest.de>
 * @license  MIT <opensource.org/licenses/MIT>
 * @link     https://jkniest.de
 */
class JsonUser implements User
{
    /**
     * @var array
     */
    private $original;

    /**
     * JsonUser constructor.
     *
     * @param array $original The original json data
     */
    public function __construct(array $original)
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
        return $this->original['id'];
    }

    /**
     * Get the user name.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->original['username'];
    }
}
