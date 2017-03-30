<?php

return array(


    'config' => [
        'hosts'     => [env('ELS_SERVER','10.1.3.7:9200')],
        'retries'   => 1,
    ],

    'max_result' => env('ELS_MAX_RESULT',20),

    /*
    |--------------------------------------------------------------------------
    | Indexes Name
    |--------------------------------------------------------------------------
    |
    */

    'default_index' => env('ELS_INDEX','edm'),
    'user_index' => env('ELS_INDEX_USER','edm-user'),


    /*
    |--------------------------------------------------------------------------
    | Types Name
    |--------------------------------------------------------------------------
    |
    */

    'user_type' => env('ELS_TYPE_PROFILE','profile'),
    'permission_type' => env('ELS_TYPE_PERMISSION','permission'),
    'session_type' => env('ELS_TYPE_SESSION','session'),
    'role_type' => env('ELS_TYPE_ROLE','role'),

);
