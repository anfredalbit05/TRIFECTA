<?php

/*
|--------------------------------------------------------------------------
| Satellite memberships
|--------------------------------------------------------------------------
|
| Single source of truth for the satellite tier lookup and for how many
| FREE members (RoleID 4) a satellite receives once its own activation is
| approved. SatelliteModal picks the tier, satellites.satellite_typeID
| stores it, and ActivationApprovalService provisions the free members.
|
*/

return [

    /*
    |--------------------------------------------------------------------------
    | satellites.satellite_typeID → tier label
    |--------------------------------------------------------------------------
    */
    'tier_by_type' => [
        6 => 'Bronze',
        7 => 'Silver',
        8 => 'Gold',
    ],

    /*
    |--------------------------------------------------------------------------
    | Free members provisioned per tier
    |--------------------------------------------------------------------------
    */
    'free_slots' => [
        'Bronze' => 20,
        'Silver' => 40,
        'Gold' => 80,
    ],

];
