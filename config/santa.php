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
    ]

];