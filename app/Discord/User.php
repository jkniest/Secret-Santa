<?php

namespace App\Discord;

/**
 * This is a representation of a user.
 *
 * @category Core
 * @package  SecretSanta
 * @author   Jordan Kniest <contact@jkniest.de>
 * @license  MIT <opensource.org/licenses/MIT>
 * @link     https://jkniest.de
 */
interface User
{
    /**
     * Get the user id.
     *
     * @return string
     */
    public function getId();

    /**
     * Get the user name.
     *
     * @return string
     */
    public function getUsername();
}
