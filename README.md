# IDServer SDK

> This project is a set of classes created for the Laravel framework as a package, allowing you to connect to the IDServer API in a very easy and fluent way.

## Installation

The SDK package is in a private Git repository, so you must include it in the `repositories` section on your `composer.json` file, then require the `xingo/idserver-sdk` package.

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/PH-F/idserver-sdk"
        }
    ],
    "require": {
        "xingo/idserver-sdk": "dev-master"
    }
}
```

## Introduction

This section is responsible for explaining the basic concepts behind the SDK, how the `Manager` class works, how the HTTP calls are made and also how to retrieve resource instances according to what you want to call in the API.

### Service Provider

This package has a `ServiceProvider.php` class that basically:

1. Registers some instances in the Laravel's service container;
2. Publishes the basic IDServer configuration file;
3. Configures the `Guzzle` instance setting authentication headers;
4. Setup queue to send the `cli` to the header, allowing the Job to be executed without authentication. 

#### Instances

The first instance in the Service Container is a singleton of `idserver.client`. It's basically a `Guzzle` instance with some headers and configs according to the environment the app is being executed:

- The `base_url` of where the IDServer should be accessed;
- A Guzzle `handler` that contains some custom middleweres and also a formatted JSON response format;
- Some custom `headers` for authentication via `web` or `cli`. Both you need two headers: `X-XINGO-Client-ID` and `X-XINGO-Secret-Key`.

The second instance in the Service Container is a singleton of `idserver.manager`. The `Manager` class is the main one, responsible for calling resources directly to the API, requesting JSON responses and sending all necessary params and headers to the request. This class will be better explained below.

### The `ids()` Helper Function

To make it easier to use, the `Manager` class (the most important one) is returned from the Service Container through the PHP function `ids()` (from IDServer). Every time you need to call a resource or response in the IDServer API you only need to use `ids()` to retrieve the instance ready to go. 

### The Manager Class

The `Manager` class is the one responsible for all subsequent calls to the API. It receives a Guzzle `Client` instance in the constructor, also from the Service Container. This HTTP instance is responsible for adding all the necessary headers to the request.

Basically, the `Manager` class does not have much logic in it, but it uses two traits that are very important during the process:

- `TokenSupport`: a set of methods responsible for managing the JWT token in the app's session. Every time a user is logged in the IDServer it returns a JWT (token) that we store in the current app's session, allowing us to reuse the same token for next calls.
- `CallableResource`: basically two magic methods `__get()` and `__call()`. Let's say we call `ids()->users`. We're reaching the `__get()` magic method, to a `src/Resources/User.php` instance is returned. Then you can call any other resource method after that.  

### The Resources Classes

Resources are classes that maps the principal endpoints the IDServer has, for each resource. For example, for the `/users/1` call in the API we have the `User` resource with a `get()` method. 

Every action in a specific resource, let's say the user with ID equal 1, you can use the resource call as a method instead of a property, sending as parameter the user ID:

```php
$user = ids()->users(1)->get();
```

Otherwise if the call does not need a specific resource, just call the method you want:

```php
$result = ids()->users->login('foo@example.com', 'secret');
``` 

### Stores, Authentication and Authorization

The IDServer API can be called by N numbers of clients, like many stores or websites. That's why we have the concept of "Stores" in the IDServer. Each call must come from a trusted store, so we need a "Client ID" and a "Secret Key". Each one can have two different roles: "web" and "cli".

> Everything is made automatically according the the Laravel app that's running this SDK. You don't have to worry about request headers or JWT when using the SDK.

#### Web Stores

Web stores are used when the user logs in with their credentials and wants to manage their data. So the SDK must send both the "Client ID", the "Secret Key", and also the user token, called JWT. JWT is a token that contains information about the user and expiration time, for example. For more information about what the JWT is please access [https://jwt.io]. In the SDK context every time a user does a "login" action, the API returns a valid JWT token and it is stored in the current session.

```php
ids()->users->login('foo@example.com', 'secret');
```

All following call to the API will include automatically the necessary JWT for authentication:

```php
$user = ids()->users->login('foo@example.com', 'secret'); // $user->jwtToken()
$users = ids()->users->all();
```

> One important point here is that once the JWT is stored in the session you don't have to login the user every time to perform some action. The SDK automatically will check if there's a valid JWT token in the session. If so it will add it to the header, allowing you to do the API call. Otherwise it will throw a `MissingJwtException`.

##### Token Refresh

Every JWT has a expiration time. The SDK knows that and has two Client Middlewares for Guzzle that are pushed when the Guzzle instance is created. They're always checking for JWT in the session and also JWT changes.

Internally the IDServer API returns a `token_expired` JSON response when the JWT is expired. Once the SDK automatically is looking for these type of changes, if that JSON response is present it - in the background -  calls the "refresh" action in the IDServer, gets a new JWT and replace it in the current session, then call the action again, returning the correct response.

> All the JWT refresh process in made in background, you never have to worry about that when using this SDK. 

#### CLI Stores

Sometimes is necessary to have some CLI apps doing something using the IDServer API. In this case, we don't have a logged user, so we need a CLI client/key for that. If the call comes from a valid CLI Store we allow it without JWT authentication. All the necessary headers are create automatically according the environment the PHP is running. If the app is running in console mode, we get the CLI Client ID and Secret Key from the config file. 

#### JWT Authenti



