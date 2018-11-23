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

### The config file

This SDK comes with a custom config file for Laravel apps. To install it you must run the following command in the terminal:

```
php artisan vendor:publish --tag=config
```

This will create a `config/idserver.php` file in your Laravel app. This file has some important settings:

- `url`: it is the URL where the IDServer is installed;

- `store`: the keys related to the store you are using with this SDK. Here you have two configurations: `web` and `cli`. `web` keys allow the store to call the IDServer using a web interface, like a web app, and `cli` keys allow the store to call the IDServer using the command line. For both settings ask Elektor/Xingo for those keys. They must be generated using the `generate:store-token {store} {--role=web}` command in the IDServer installation (not the SDK one).

- `classes`: this configuration is a simple array to map entities and responses. By default each API call returns a `Entity` instance, but here you can ask for a custom class, like a Laravel model for example. This way you can call `ids()->users(1)->get()` and will get a `App\User` model instance, for example. For more information, take a look on [Entities Classes](#user-content-entities-classes) section.

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

##### The `Auth` Middleware

This SDK comes with a handful `Auth` middleware, that must be used in all actions you want to protect by the JWT authentication. You can name it in you Laravel `Http/Kernel.php` file, for example, like `jwt-auth` or just replacing the current `auth` one.

##### Token Refresh

Every JWT has a expiration time. The SDK knows that and has two Client Middlewares for Guzzle that are pushed when the Guzzle instance is created. They're always checking for JWT in the session and also JWT changes.

Internally the IDServer API returns a `token_expired` JSON response when the JWT is expired. Once the SDK automatically is looking for these type of changes, if that JSON response is present it - in the background -  calls the "refresh" action in the IDServer, gets a new JWT and replace it in the current session, then call the action again, returning the correct response.

> All the JWT refresh process in made in background, you never have to worry about that when using this SDK. 

#### CLI Stores

Sometimes is necessary to have some CLI apps doing something using the IDServer API. In this case, we don't have a logged user, so we need a CLI client/key for that. If the call comes from a valid CLI Store we allow it without JWT authentication. All the necessary headers are create automatically according the environment the PHP is running. If the app is running in console mode, we get the CLI Client ID and Secret Key from the config file. 

#### Authorization

All IDServer authorization is made using the concept of "roles" and "abilities". You have basic roles with some default abilities, but this IDServer also allows you to add custom abilities to a given user. All abilities are returned as a Laravel collection. To give the current abilities a user has:

```php
ids()
    ->users(1)
    ->abilities() // Collection of abilities
    ->each(function ($ability) {
        var_dump($ability->name); // foo.bar
    });
```

To retrieve all available abilities:

```php
$first = ids()->abilities->all()->fist();
echo $first->name; // foo.bar
```

> If you have access to the IDServer repository, take a look on the `database/seeds/data/abiliites.php` file. You'll see all the abilities available in the IDServer API.

### Entities and Collections

#### Entities Classes <a name="entities-classes"></a>

All IDServer response is in JSON response. To make things a bit easier we always return simples classes called "Entities". So if you are retrieving a user data you'll get a User Entity as response. Each `Entity` class has a set of custom methods, like the `User::name()` for example. This is the default behavior, making the SDK returning you simple entity classes.

If you want to customize the returned class you can map one by one in the config file. Let's say your Laravel app already have a `User` model, and every time you call `ids()->users(5)->get()` you don't want a user entity instance, but a `User` model. You can inform the SDK which class it should instantiate for you, mapping all the properties automatically.

In the config file:

```php
'classes' => [
    \Xingo\IDServer\Entities\User::class => App\Models\User::class,
],
```

Here you're saying that instead of getting a `Entities\User` instance you want a `App\Models\User` instance. Then you can use your own class with custom methods and logic.

#### Collections

The IDServer API uses the "JSON Resources" concept in Laravel. So when returning collections it also add a `meta` field with some useful information about pagination, next/previous URL, pages, total of pages, etc. To make sure this information is present in the returned object we created a custom collection class, that also have a `$meta` property, a simple object with all those data from the API.

```php
$users = ids()->users->all();
var_dump($users->meta);
```

> The custom Collection is exactly like the base Laravel's collection, but with the `$meta` property mapped with the JSON response.

### Resources Concepts

#### The base Resource abstract class

To make possible to call IDServers endpoints, sending always the correct information, and also expecting some formatted result, we created the abstract `Resource` class. All following resource classes must extends `Resource` to make integration with the backend easier.

This class has some important helper methods and each one has his own explanation. Here you can find some information about the most important ones:

- `__invoke()`: this method makes possible to inform an individual resource in a "callable" way. It's the one responsible for making calls like `->users(1)` possible. It temporarily stores the desired ID, and then when calling the action, it's added. It's just to make the calling process easier and prettier.

- `call()`: this method is used internally only, but here is where we make the request and process the response (both using GuzzleHttp). The response is returned but the `$this->content` property is filled with the JSON response, the content itself, making easier for further manipulation.

- `makeEntity()`: this method is the one responsible for returning the correct `Entity` instance to the user. It basically gets the content from response and call the `EntityCreator` class sending the data received. If you are calling an endpoint using a `UserResource` this is the method that is going to return you a `User` entity instance.

- `makeCollection()`: this method acts like the `makeEntity()` one, but for collections, appending also the `meta` values if present in the response data. 

#### Helper Traits

##### `ResourceBlueprint`

Usually some endpoints has common actions, like creating a new resource, listing multiple resources, deleting, etc. Those common actions have almost the same request/response workflow, so that is why the `ResourceBlueprint` exits. It is responsible for adding common behavior to resource classes, using the following methods:

- `all(array $filters = []): Collection`: responsible for listing multiple resources. It can be literally all, or just some, using pagination (will be explained later on) for example. As you can see this method also return a `Collection` of entities (respecting the same resource type).

- `get(): IdsEntity`: responsible for returning a single resource. If you call `->users(1)->get()` it is expected to get the `User` entity with ID 1.

- `create(array $attributes): IdsEntity`: responsible for creating a new resource with some attributes. It is also expected to get the created entity as return.

- `update(array $attributes): IdsEntity`: responsible for updating a given resource and get it returned. Must be used for a single resource, for example `->users(1)->update([])`.

- `delete(): bool`: responsible for deleting a resource. The result is just `true` or `false`. Example: `->users(1)->delete()`.

Basically, all resource classes use the `ResourceBlueprint` trait. When it's necessary a custom parameter or changing some request/response, the necessary methods are just overridden.

##### `FilteredQuery` and `ResourceOrganizer`

`FilteredQuery` is a trait used by the `ResourceBlueprint` one that is responsible for manipulating the query string that will be sent in the request. It also uses the `ResourceOrganizer` trait, that's basically responsible for adding pagination (by the `paginate($page, $per_page)` method), sorting (by the `sort($field, string $order = 'asc')` method) and filtering features to any resource class.

If you want to paginate results for subscriptions, for example:

```php
$collection = ids()->subscriptions
    ->paginate(10)
    ->all();
```

And for adding sorting and filtering features:

```php
$collection = ids()->subscriptions
    ->paginate(1, 10)
    ->sort('start_date', 'desc')
    ->all(['user_id' => 1]);
```

In this case we are requesting all subscriptions from the user ID 1, ordered descending by the `start_date` field, and asking for the first page, paginated with 10 subscriptions on each page.

