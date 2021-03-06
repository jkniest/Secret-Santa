<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * The states are a simple key, value collection to save some imported data, such as the
 * bot status or some discord message id's.
 *
 * @category Core
 * @package  SecretSanta
 * @author   Jordan Kniest <contact@jkniest.de>
 * @license  MIT <opensource.org/licenses/MIT>
 * @link     https://jkniest.de
 */
class State extends Model
{
    const STOPPED = 1;

    const STARTED = 2;

    const DRAWING = 3;

    const IDLE = 4;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the value of a state by it's name.
     *
     * @param string $name The name of the state
     *
     * @return string
     */
    public static function byName(string $name)
    {
        return static::whereName($name)->first()->value;
    }

    /**
     * Set the value of a given state.
     *
     * @param string      $name  The name of the state
     * @param string|null $value The new value
     *
     * @return void
     */
    public static function set(string $name, $value)
    {
        static::whereName($name)->first()->update([
            'value' => $value
        ]);
    }
}
