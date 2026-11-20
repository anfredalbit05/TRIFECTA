<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Withdrawal Request Window — Timezone
    |--------------------------------------------------------------------------
    |
    | The program accepts withdrawal requests from Friday 5:01 PM to Sunday
    | 12:00 AM (Monday midnight). Those clock times are the members' local
    | time, so the window is evaluated in this timezone (not the server's
    | UTC clock). Change it here if the program moves to another market.
    |
    */

    'timezone' => env('WITHDRAWAL_TIMEZONE', 'Asia/Manila'),

];
