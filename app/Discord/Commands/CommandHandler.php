<?php

namespace App\Discord\Commands;

use App\Discord\MessageHandler;

/**
 * The command handler can convert default discord messages into commands and execute them.
 *
 * @category Core
 * @package  SecretSanta
 * @author   Jordan Kniest <contact@jkniest.de>
 * @license  MIT <opensource.org/licenses/MIT>
 * @link     https://jkniest.de
 */
class CommandHandler
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @var \Illuminate\Support\Collection
     */
    private $commands;

    /**
     * CommandHandler constructor.
     *
     * @param string $prefix   The global command prefix
     * @param array  $commands All commands
     */
    public function __construct(string $prefix, array $commands)
    {
        $this->prefix = $prefix;
        $this->commands = collect($commands);
    }

    /**
     * Map the given message to a command and execute it. If no command is found for
     * this specific message, nothing will be returned.
     *
     * @param MessageHandler $message The given message
     *
     * @return mixed|null
     */
    public function handle(MessageHandler $message)
    {
        /** @noinspection PhpUnusedParameterInspection */
        $command = $this->commands->first(function ($cmd, $key) use ($message) {
            return starts_with("{$this->prefix} {$key}", $message->getContent());
        });

        return $command ? (new $command($message))->handle() : null;
    }
}
