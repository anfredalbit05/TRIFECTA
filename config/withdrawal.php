<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Withdrawal Request Window — Timezone
    |--------------------------------------------------------------------------
    |
    | The program accepts withdrawal requests from Friday 5:00 PM to Sunday
    | 11:59 PM (Monday midnight). Those clock times are the members' local
    | time, so the window is evaluated in this timezone (not the server's
    | UTC clock). Change it here if the program moves to another market.
    |
    */

    'timezone' => env('WITHDRAWAL_TIMEZONE', 'Asia/Manila'),

];
