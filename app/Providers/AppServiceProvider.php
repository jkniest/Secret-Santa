<?php

namespace App\Providers;

use App\Discord\DiscordMessageService;
use App\Discord\MessageService;
use Illuminate\Support\ServiceProvider;

/**
 * The main service provider.
 *
 * @category Core
 * @package  SecretSanta
 * @author   Jordan Kniest <contact@jkniest.de>
 * @license  MIT <opensource.org/licenses/MIT>
 * @link     https://jkniest.de
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(MessageService::class, DiscordMessageService::class);
    }
}
