<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Start date
    |--------------------------------------------------------------------------
    |
    | On this date the bot will automatically start if the announcement channel
    | has been set.
    |
    */
    'start'             => [
        'day'   => env('START_DAY', 1),
        'month' => env('START_MONTH', 12),
        'hour'  => env('START_HOUR', 15)
    ],

    /*
    |--------------------------------------------------------------------------
    | End participation date
    |--------------------------------------------------------------------------
    |
    | On this date the participation phase will stop. This will happen via
    | an automatic cronjob.
    |
    */
    'end_participation' => [
        'day'   => env('END_PARTICIPATION_DAY', 23),
        'month' => env('END_PARTICIPATION_MONTH', 12),
        'hour'  => env('END_PARTICIPATION_HOUR', 15)
    ],

    /*
    |--------------------------------------------------------------------------
    | Draw date
    |--------------------------------------------------------------------------
    |
    | On this date every participant will get a partner and a DM with the
    | partner. This will happen via an automatic cronjob.
    |
    */
    'draw'              => [
        'day'   => env('DRAW_DAY', 24),
        'month' => env('DRAW_MONTH', 12),
        'hour'  => env('DRAW_HOUR', 15)
    ],

    /*
    |--------------------------------------------------------------------------
    | Give date
    |--------------------------------------------------------------------------
    |
    | On this date every participant will get a direct message with the info
    | that they should now give their presents to their partners. Also a new
    | announcement post will be send.
    |
    */
    'give'              => [
        'day'   => env('GIVE_DAY', 31),
        'month' => env('GIVE_MONTH', 12),
        'hour'  => env('GIVE_HOUR', 15)
    ]

];