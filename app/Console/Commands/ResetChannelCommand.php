<?php

namespace App\Console\Commands;

use App\Models\State;
use Illuminate\Console\Command;

/**
 * This artisan command removes the announcement channel from the database so that the
 * !santa mark discord command can be executed again.
 *
 * It will only be executed if the bot state is stopped.
 *
 * @category Core
 * @package  SecretSanta
 * @author   Jordan Kniest <contact@jkniest.de>
 * @license  MIT <opensource.org/licenses/MIT>
 * @link     https://jkniest.de
 */
class ResetChannelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:channel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the announcement channel';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        State::set('announcement_channel', null);
    }
}
