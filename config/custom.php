<?php


return [

    /*
    |--------------------------------------------------------------------------
    | This file is for mapping custom environment variables that don't belong to any third party services (otherwise we'd have just put those variables (even if they were custom env variables) in config > services.php file, etc.)
    |--------------------------------------------------------------------------

    */



    "otp_values" => [
        "start" => env("OTP_START_VALUE", 000000),
        "end" => env("OTP_END_VALUE", 999999),
    ]
];
