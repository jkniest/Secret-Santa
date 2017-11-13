<?php

return [

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
        'day'   => env('END_PARTICIPATION_DAY', 24),
        'month' => env('END_PARTICIPATION_MONTH', 12),
        'hour'  => env('END_PARTICIPATION_HOUR', 15)
    ]

];