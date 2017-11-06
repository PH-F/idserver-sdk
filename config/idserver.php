<?php

return [

    /*
    |--------------------------------------------------------------------------
    | IDServer base URL
    |--------------------------------------------------------------------------
    |
    | This should be set with the IDServer API base endpoint. So
    | if you would like to get all users using http://example.com/api/v1/users
    | this option should be set to `http://example.com/api/v1/` and
    | just use `/users` to get all users.
    |
    */

    'url' => 'http://idserver.xingo.nl/v1/',

    /*
    |--------------------------------------------------------------------------
    | Store Authentication
    |--------------------------------------------------------------------------
    |
    | Each Store has to be authenticated to ensure the request is coming
    | from a valid client. The authentication is made with a combination of
    | a "Client ID" and a "Secret Key".
    |
    */

    'store' => [

        /*
        |--------------------------------------------------------------------------
        | Client ID
        |--------------------------------------------------------------------------
        |
        | The Client ID is used to identify the Store making the call to the
        | API. It's a required value.
        |
        */

        'client_id' => '',

        /*
        |--------------------------------------------------------------------------
        | Secret Key
        |--------------------------------------------------------------------------
        |
        | This is the key associated with the "Client ID". Both field should
        | match.
        |
        */

        'secret_key' => '',

    ]

];
