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

    'url' => env('IDSERVER_URL', 'https://api-idserver.xingo.nl/v1/'),

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

        'client_id' => env('IDSERVER_CLIENT_ID'),

        /*
        |--------------------------------------------------------------------------
        | Secret Key
        |--------------------------------------------------------------------------
        |
        | This is the key associated with the "Client ID". Both field should
        | match.
        |
        */

        'secret_key' => env('IDSERVER_SECRET_KEY'),

    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Class Instances
    |--------------------------------------------------------------------------
    |
    | When using the IDServer API you receive entities classes, like
    | Xingo\IDServer\Entities\User, for example. You can customize this
    | setting adding a new relation to your custom class, allowing you to customize
    | the returned class to another one from your app, not the SDK package,
    | making possible to add new methods, properties and custom logic.
    |
    */

    'classes' => [
//        \Xingo\IDServer\Entities\User::class => App\Entities\User::class,
//        \Xingo\IDServer\Entities\Subscription::class => App\Entities\Subscription::class,
    ],

];
