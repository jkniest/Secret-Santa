<?php

namespace App\Providers;

use App\Discord\DiscordMessageService;
use App\Discord\MessageService;
use Carbon\Carbon;
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
        Carbon::setLocale('de_DE');
        setlocale(LC_TIME, 'de_DE');
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
