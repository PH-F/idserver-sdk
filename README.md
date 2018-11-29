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

This section is responsible for explaining the basic concepts behind the SDK, how the `Manager` class works, how the HTTP calls are made and also how to retrieve resource instances depending on what you want to call in the API.

### Service Provider

This package has a `ServiceProvider.php` class that basically:

1. Registers some instances in the Laravel's service container;
2. Publishes the basic IDServer configuration file;
3. Configures the `Guzzle` instance setting authentication headers;
4. Setup queue events to send the `cli` to the header, allowing the Job to be executed without authentication. 

#### Instances

The first instance in the Service Container is a singleton of `idserver.client`. It's basically a `Guzzle` instance with some headers and configs according to the environment the app is being executed:

- The `base_url` of where the IDServer should be accessed;
- A Guzzle `handler` that contains some custom middleweres and also a formatted JSON response format;
- Some custom `headers` for authentication via `web` or `cli`. Both need two headers: `X-XINGO-Client-ID` and `X-XINGO-Secret-Key`.

The second instance in the Service Container is a singleton of `idserver.manager`. The `Manager` class is the main one, responsible for calling resources directly to the API, requesting JSON responses and sending all necessary params and headers to the request. This class will be better explained below.

### The `ids()` Helper Function

To make it easier to use, the `Manager` class (the most important one) is returned from the Service Container through the PHP function `ids()` (from IDServer). Every time you need to call a resource or response in the IDServer API you only need to use `ids()` to retrieve the instance ready to go. 

### The Manager Class

The `Manager` class is the one responsible for all subsequent calls to the API. It receives a Guzzle `Client` instance in the constructor, also from the Service Container. This HTTP instance is responsible for adding all the necessary headers to the request.

Basically, the `Manager` class does not have much logic in it, but it uses two traits that are very important during the process:

- `TokenSupport`: a set of methods responsible for managing the JWT token in the app's session. Every time a user is logged in the IDServer it returns a JWT (token) that we store in the current app's session, allowing us to reuse the same token for next calls.
- `CallableResource`: basically two magic methods `__get()` and `__call()`. Let's say we call `ids()->users`. We're reaching the `__get()` magic method, which will return a `src/Resources/User.php` instance. You can call any method of this resource after that, like: `ids()->users->create([...])`.  

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

Resources are classes that map the principal endpoints the IDServer has, for each resource. For example, for the `/users/1` call in the API we have the `User` resource with a `get()` method. 

For every action in a specific resource ()let's say the user with ID equal 1) you can use the resource call as a method instead of a property. Like in the following example where we are sending the user ID as parameter:

```php
$user = ids()->users(1)->get();
```

Otherwise if the call does not need a specific resource, just call the method you want like:

```php
$result = ids()->users->login('foo@example.com', 'secret');
``` 

### Stores, Authentication and Authorization

The IDServer API can be called by N numbers of clients. That's why we have the concept of "Stores" in the IDServer. Each call must come from a trusted store, so we need a "Client ID" and a "Secret Key". Each one can have two different roles: "web" and "cli".

> Everything is configured automatically by the Laravel app that's running this SDK. You don't have to worry about request headers or JWT when using the SDK.

#### Web Stores

Web stores are used when the user logs in with their credentials and want to manage their data. So the SDK must send both the "Client ID", the "Secret Key" and also the user token, called JWT. JWT is a token that contains information about the user and expiration time, for example. For more information about what the JWT is, please access [https://jwt.io]. In the SDK context every time a user does a "login" action, the API returns a valid JWT token and it is stored in the current session.

```php
ids()->users->login('foo@example.com', 'secret');
```

All following calls to the API will automatically include the necessary JWT for authentication:

```php
$user = ids()->users->login('foo@example.com', 'secret'); // $user->jwtToken()
$users = ids()->users->all();
```

> One important point here is that once the JWT is stored in the session you don't have to login the user every time again to perform some action. The SDK automatically will check if there's a valid JWT token in the session. If so it will add it to the header, allowing you to do the API call. Otherwise it will throw a `MissingJwtException`.

##### The `Auth` Middleware

This SDK comes with a useful `Auth` middleware that should be used in all actions you want to protect by the JWT authentication. You can add it in your Laravel `Http/Kernel.php` file, for example, like `ids-auth` or just replacing the current `auth` one.

```php
protected $routeMiddleware = [
    // ...
    'ids-auth' => \Xingo\IDServer\Middleware\Auth::class,
    // ...
];
```

##### Token Refresh

Every JWT has an expiration time. The SDK knows that and has two Client Middlewares for Guzzle which are always checking for JWT in the session and also JWT changes.

Internally the IDServer API returns a `token_expired` JSON response when the JWT is expired. Once the SDK automatically is looking for these type of changes, if necessary, it automatically calls the "refresh" action in the IDServer, getting a fresh token, updating it in the current session. Then it calls the first action again with the new token, returning the correct response this time.
Internally the IDServer API returns a `token_expired` JSON response when the JWT is expired. Once the SDK automatically is looking for these type of changes, if necessary, it automatically calls the "refresh" action in the IDServer, getting a fresh token, updating it in the current session. Then it calls the first action again with the new token, returning the correct response this time.

> All the JWT refresh process in made in background, you never have to worry about that when using this SDK. 

#### CLI Stores

Sometimes it's necessary to have some CLI apps doing something using the IDServer API. In this case, we don't have a logged user, so we need a CLI client/key for that. If the call comes from a valid CLI Store we allow it without JWT authentication. All the necessary headers are created automatically according the environment the PHP is running in. If the app is running in console mode, we get the CLI Client ID and Secret Key from the config file. Otherwise the normal web Client ID and Secret Key are being used. 

#### Authorization

All IDServer authorization is made using the concept of "roles" and "abilities". You have basic roles with some default abilities, but the IDServer also allows you to add custom abilities to a given user. All abilities are returned as a Laravel collection. To retrieve all abilities a user has:

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

All IDServer response is in the JSON format. To make things a bit easier we always return simples classes called "Entities". So if you are retrieving user data you'll get a User Entity as response. Each `Entity` class has a set of custom methods, like the `$user_entity->getName()` for example. This is the default behavior, making the SDK returning you simple entity classes.

If you want to customize the returned class you can map them one by one in the config file. Let's say your Laravel app already has a `User` model, and every time you call `ids()->users(5)->get()` you don't want a user entity instance, but a `User` model. You can inform the SDK which class it should instantiate for you, mapping all the properties automatically.

In the config file:

```php
'classes' => [
    \Xingo\IDServer\Entities\User::class => App\Models\User::class,
],
```

Here you're saying that instead of getting a `Entities\User` instance you want a `App\Models\User` instance. Now you can use your own class with custom methods and logic.

#### Collections

The IDServer API uses the "JSON Resources" concept of Laravel. So when returning collections it also adds a `meta` field with some useful information about pagination, next/previous URL, pages, total of pages, etc. To make sure this information is present in the returned object we created a custom collection class that has a `$meta` property, a simple object with all this data from the API.

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

## Available Resources

### Ability (`abilities`)

Retrieves abilities from the IDServer. Usually, this resource needs to be called with `->all()` for listing all available abilities in the IDServer:

```php
$collection_of_abilities = ids()->abilities->all();
// With pagination
$collection = ids()->abilities->paginate(1, 10)->all(); // page 1, 10 per page
```

The `Ability` resource uses the `ResourceBlueprint`, so you also have access to `get()`, `create()`, `update()` and `delete()` methods.

**Parameters**

Parameter | Type | Required | Description
--------- | ---- | -------- | -----------
name | string | Yes | The ability name in snake_case format, like `create_user`.
title | string | No | Just a title for that ability, like 'Creates a new user'.

### Address (`addresses`)

This resource is responsible for dealing with addresses in the IDServer. This class has its own `create()` method, basically because when creating a new address we need a nested resource, for example, "user" or "company". The final endpoint is something like `POST /users/1/addresses`.

```php
$new_address = ids()->users(1)
    ->addresses
    ->create($params);
```

**Parameters**

Parameter | Type | Required | Description
--------- | ---- | -------- | -----------
type | string | No | The address type. Default to `extra`. Possible values: `contact`, `extra`, `shipping` and `invoice`.
first_name | string | No | The user's first name.
middle_name | string | No | The user's middle name.
last_name | string | No | The user's last name.
company | string | No | The company's name.
department | string | No | The company's department name, if any.
street | string | Yes | The street name.
street_addition | string | No | Some other information for the street.
house_number | string | No | The house number.
house_letter | string | No | The house letter, if any.
city | string | Yes | The city name.
province | string | No | The province.
postcode | string | Yes | The postcode.
country_id | integer | Yes | The country number in IDServer.
latitude | decimal | No | The latitude.
longitude | decimal | No | The longitude.

**Filters**

Filter | Type | Description
--------- | ---- | -----------
type | string | Filter by address' type.
address | string | Filter by part of the address. This will search on all the following fields: `street`, `street_addition`, `house_number` and `house_letter`. 
city | string | Filter by the city name.
province | string | Filter by the province name.
postcode | string | Filter by the postcode.
country | string | Filter by the country name.
latitude | string/float | Filter by the latitude value.
longitude | string/float | Filter by the longitude value.

### Communication (`communications`)

This resources is responsible for dealing with the communications table in IDServer. Like the `Address` one it requires a nested resource to work ("user" or "company"):

```php
$communication = ids()->communications
    ->companies(2)
    ->communications
    ->create([
        'type' => 'email',
        'value' => 'foo@example.com',
        'is_primary' => false,
    ]);
```

**Parameters**

Parameter | Type | Required | Description
--------- | ---- | -------- | -----------
type | string | Yes | The communication type. Possible values are `phone`, `email`, `mobile`.
value | string | Yes | The value itself, the phone number or the email, for example.
is_primary | boolean | No | If that's the primary communication or not.

### Company (`companies`)

This resource is responsible for dealing with "companies" information. A user belongs to a company, so he/she is part of one. Besides all the actions provided by the `ResourceBlueprint` trait, this resource also has `addresses()` and `communications()` methods, so you can get these information from a given company.

```php
$company = ids()->companies(10)->get();
$addresses = ids()->companies(10)->addresses();
$communications = ids()->companies(10)->communications();
```

**Parameters**

Parameter | Type | Required | Description
--------- | ---- | -------- | -----------
name | string | Yes | The company's name
vat | string | No | The company's VAT number

### Country (`countries`)

This resource deals with the `countries` endpoint on IDServer API. Unlike some other resource, it does not use the `ResourceBlueprint` trait, and has only a single method: `all()`. It also uses the `FilteredQuery` trait, so you also have access to pagination and filtering. About filtering, you can filter countries by name.

```php
$allCountries = ids()->countries->all();
$firstTenCountries = ids()->countries->paginate(1, 10)->all();
$filteredCountries = ids()->countries->all(['name' => 'china']);
```

**Parameters**

Parameter | Type | Required | Description
--------- | ---- | -------- | -----------
code | string | Yes | The country's 2-digit code like `nl`, `us` or `br`
name | string | Yes | The country's name

### Coupon (`coupons`)

This resource is responsible for the `coupons` endpoint on the IDServer API. You can get information about coupons in a very easy way. It uses all the actions provided by the `ResourceBlueprint` trait.

```php
$coupon = ids()->coupons(8)->get();
$paginated = ids()->coupons->paginate(2, 5)->all();
ids()->coupons(2)->delete(); // true or false
```

**Parameters**

Parameter | Type | Required | Description
--------- | ---- | -------- | -----------
promotion_id | integer | Yes | The promotion ID that the coupon is attached to
code | string | Yes | The code for the coupon, something like `black-friday-18`, for example.
usage_limit | integer | Yes | The amount of times this coupon can be used. Default to `0`.
times_used | integer | No | This parameters is not used publicity, but it is incremented each time a coupon is used, internally.

### Duration (`durations`)

A Plan Duration is the lowest level of a plan representation. A Plan has many Plan Variations, and a Plan Variation has many Plan Durations. Basically the duration it responsible for determining the length of the subscription will have. The `Duration` resource class has all the methods provided by the `ResourceBlueprint` trait.

```php
$duration = ids()->durations(1)->get();
$updatedDuration = ids()->durations(1)->update(['name' => 'Foo bar']);
``` 

![plans](https://i.imgur.com/g1RZRYF.png)
> The image above shows show plans are structured and organized. Source: https://www.elektormagazine.com/account/subscription/add/15#subscription-configurator

**Parameters**

Parameter | Type | Required | Description
--------- | ---- | -------- | -----------
is_active | boolean | No | If the plan duration is active. Default to `true`.
is_default | boolean | No | If it is the default one to be shown to the user. Default to `false`.
is_hidden | boolean | No | if it should be hidden from the user. Default to `false`.
plan_variant_id | integer | Yes | The plan variant ID it belongs to.
name | string | Yes | The plan duration name, like "One year" or "Two years".
description | string | No | An optional description to this plan duration, to make it easier to remember, for example.
duration | integer | Yes | The amount of months that duration has, for example `24` or `12` (months, always).
position | integer | No | The give order to display options to the user. 1 for the first one, for example.
discount | array | Yes | This discount in fiat currency. The key must be the currency code and the value the amount of discount. Example: `['USD' => 1000]` for USD10 discount.

### FeaturedPlan (`featuredPlans`)

This resource is responsible for managing the featured plans, for a home page, for example. The only reference to the plan is through the `plan_duration_id`. It has all the methods provided by the `ResourceBlueprint` trait, including pagination.

```php
$all = ids()->featuredPlans->all($filters);
```

**Filters**

Filter | Type | Description
--------- | ---- | -----------
store | integer/string | A single or a list of stores IDs separated by comma. Example: 1 or "1,2,3,4,5".
is_active | boolean | True only for active featured plans or false to non actives.

**Parameters**

Parameter | Type | Required | Description
--------- | ---- | -------- | -----------
is_active | boolean | No | If the featured plan is active. Default to `false`.
store | array | Yes | An array of stores IDs that featured plan belongs to.
plan_duration_id | integer | Yes | The plan variant ID it belongs to.
title | string | Yes | The title that will be shown to the end user.
position | integer | No | The position to be ordered to the end user.
image | string | Yes | The image URL to represent that plan.
text | string | Yes | The text to be shown to the end user.
details | array | Yes | An array of details (in the "What you get:" section)

![featured plans](https://i.imgur.com/podQQEG.png)

### Grid (`grids`)

> Grids are complex and they have their own resources and filters. So if you need more information about them check their dedicated section "Working with Grids".

When displaying a huge amount of data is important to have some extra performance and extra options. This resource has two responsibilities: retrieve data from IDServer and export that data in CSV files. This resource has two methods: 

- `data(array $filters = []): Collection`: get a collection of data from a available grid on IDServer API;
- `export(array $filters = []): Closure`: get the exported CSV file data. It is returned in form of a `Closure` so you can call it whenever you want to and it will echo all the data related to the CSV file.

```php
$data = ids()->grids(1)->data($filters);
$closure = ids()->grids(1)->export($filters);
// and whenever you want to echo the CSV file
$closure();
```

> TIP: When dealing with exporting grids you can use [PHP's output buffer](https://secure.php.net/manual/en/book.outcontrol.php) to store the data in a variable and then saving locally the CSV file content, instead of calling the closure directly.

### Note (`notes`)

Notes are internal extra information that can be added to a user, for example. Notes are ready to be used by any model with just some few changes in the code, but for now it's only available for users. So this mean a logged user (internal one) can add some notes to a specific user, for example.

```php
$notes = ids()->users(1)->notes->all();
ids()->users(2)->notes->create(['text' => 'User missing contract']);
```

**Parameters**

Fields filled internally:

Parameter | Type | Description
--------- | ---- | -----------
user_id | integer | The user that added that note. Usually the current logged user when adding the note.
noteable_type | string | The model that note is for (User or something else). It's a Morph relation in Laravel.
noteable_id | integer | The id of the related model.

Public fields:

Parameter | Type | Required | Description
--------- | ---- | -------- | -----------
text | string | Yes | The text for the note.

### Order (`orders`)

This resource is responsible for the `orders` endpoint on IDServer API. It has all the methods provided by the `ResourceBlueprint` trait plus:

- `payment(array $attributes)`: allows you to update the order row with payment information;
- `price(array $attribuets)`: retrieves all price-related data, with discounts applied, promotion, etc.

#### Basic Usage

```php
$order = ids()->orders(345)->get();
```

Parameters for the order resource:

Name | Type | Required | Description
--------- | ---- | -------- | -----------
currency | string | Yes | The currency abbreviation, like EUR or USD.
user_id | integer | Yes | The user id to create an order for.
coupon | string | No | The coupon code to be applied, if any.
plan_duration_id | integer | Yes | The plan duration ID related to this order.
store_id | integer | Yes | The store ID this order belongs to.
payment_code | string | Yes | The payment method code (there is not specific values here).

#### Using the `payment()` method

```php
$updated_order = ids()->orders(123)->payment([
    'payment_number' => 871652389123,
    'status' => 'cancelled',
]);
```

> NOTE: Both `payment_number` and `status` fields are not validated on IDServer. Make sure this will be updated to improve security.

#### Dealing with `price()` data

The `price()` method return some data related to an order. First, take a look on how to use it with this SDK, the request attributes and the response data.

```php
$price_data = ids()->orders(123)->price($attributes);
```

The request attributes are (for `$attributes` variable):

Name | Type | Required | Description
--------- | ---- | -------- | -----------
currency | string | Yes | The currency abbreviation, like EUR or USD.
plan_duration_id | integer | Yes | The ID of the related plan duration.
country | string | No | The country code, like "br" or "nl".
coupon | string | No | The coupon code if you want to apply some discount, for example.
subscription_id | integer | No | The ID of the related subscription.

After calling the `price($attributes)` method you will get these data:

> All prices are in integer format with 2 digits of precision, for example: 1050 is $10.50. This was made to avoid float issues.

Name | Type | Description
--------- | ---- | -----------
currency | string | The currency abbreviation, like EUR or USD. 
plan_price | integer | The price at the plan level. 
plan_variant_price | integer | The price at the plan variant level. 
plan_duration_price | integer | The price at the plan duration level. 
shipping_cost | integer | The shipping cost. 
promotion | integer | The price of the promotion with discounts. 
discount | integer | The price of the discount. 
total_price | integer | The total net price. 
plan_duration | array | The plan duration object in array format. 
shipping_cost_name | string | The name field of the shipping cost object. 
promotion_amount | integer/null | The absolute promotion amount if any, or null.
discount_amount | integer/null | The absolute discount amount if any, or null. 

### PaymentMethod (`paymentMethods`)

There are many types of available payment methods. This resource is responsible for managing them. The only method available is `all()` without any parameter.

```php
$payment_methods = ids()->paymentMethods->all();
```

### Plan (`plans`)

This resource is responsible for managing plans at the highest level. Remember we have three levels of plans: first plans, then plans variants and then plans durations. A plan duration belongs to a plan variant, that belongs to a plan.

The `Plan` resource has all available methods provided by the `ResourceBlueprint` trait plus a `sendList(array $filters = [])` method, that's responsible for returning a CSV file with all users that you send emails to, based on active subscriptions.

> Currently, there is no support for `$filters`, so just leave it empty.

```php
$closure = ids()->plans(123)->sendList();
```

> The `sendList()` method returns a closure, so you can call it whenever you want, and the CSV file data will be echoed.

The returned CSV file will contain all the following columns related to subscriptions:

Name | Type | Description
--------- | ---- | -----------
Id | integer | The subscription ID.
User | integer | The user ID.
Start date | string | The subscriptions' start date.
End date | string | The subscriptions' end date.
Name | string | The user name to send mail to.
Company | string | The company name.
Address | string | The street name (address).
House number | string | The house number plus house letter, if any.
Address2 | string | Any other street addition.
Postcode | string | The address' postcode.
City | string | The address' city name.
Province | string | The address' province name.
Country | string | The address' country code.

### Promotion (`promotions`)

Promotions are related to coupon code. A coupon code belongs to a promotion. This resource is responsible to manage those promotions. It has all methods provided by the `ResourceBlueprint` trait, plus pagination, for example:

```php
$promotion = ids()->promotions(123)->get();
$paginated_promotions = ids()->promotions->paginate(2, 10)->all();
```

**Parameters**

Parameter | Type | Required | Description
--------- | ---- | -------- | -----------
is_active | boolean | No | If the promotion should be active or not. Default to `false`.
exclude_shipping | boolean | No | If this promotion should exclude shipping for calculation. Default to `false`.
plan_durations | array | No | An array of all plan durations that promotion should be part of.
name | string | Yes | A name for that promotion, like "Black Friday Promotion".
start_date | string | Yes | The start date of the promotion. Format: YYYY-MM-DD.
end_date | string | Yes | The end date of the promotion. Format: YYYY-MM-DD.
days_renewal_gap | integer | No | Amount of days the user still can renew after the end of the promotion.
subscription_type | string | No | The type of the subscription. Options: `new` or `renewal`. This mean this promotion can be applied only for either new or renewal subscriptions.
discount_type | string | Yes | The type of the discount. Options are `percentage` or `amount`.
discount | array | Yes | The discount to be applied in array format, with keys the currency code and value the discount (percent or amount). Example: `['USD' => '1000']`. In this case if `discount_type` is `amount` then it means $10 of discount for USD currency. Otherwise if `discount_type` is `percent` then this means "10% of discount for USD".

### Report (`reports`)

Reports are just useful data compiled in a single response. The `Report` resource file has only one action: `get(array $filters = [])`. This action is responsible for getting the desired report by its ID, but in this case the ID is not an integer, it's a string, with the report name.

Currently, the IDServer supports four different reports: `subscription-funnel`, `subscription-new`, `subscription-renewal` and `free-fall`. Each one has its own logic behind IDServer.

```php
$subscriptionFunnelReport = ids()->reports('subscription-funnel')->get();
```

All those four report types support a single filter: `plans`. In this filter you can specify which plans ids you want to filter through, separated by commas:

```php
$report = ids()->reports('free-fall')->get(['plans' => '14,198,9811']);
```

### Role (`roles`)

Role is a title that automatically gives a user some abilities. The `Role` resource has all methods provided by the `ResourceBlueprint` trait plus a `sync()` method. The `update()` method is overrode to include also an `$abilities` array, allowing you to update also a list of abilities for a given role.

```php
$role = ids()->roles(14)->get(); // get
$updatedRole = ids()->roles(15)->update(['name' => 'super-admin'], ['a very long', 'abilities', 'list']);
``` 

The `sync()` method is responsible for syncing roles with a given user, using a nested resource. So you must first tell the SDK which is the user, and then call the `sync()` method with an array of roles in string format:

```php
$rolesCollection = ids()->users(1)->roles->sync(['guest', 'admin']);
```

**Parameters**

Parameter | Type | Required | Description
--------- | ---- | -------- | -----------
name | string | Yes | The role name, like `admin`.
title | string | No | The role title, just for description purposes.

### Store (`stores`)

A Store is a single client-app where the IDServer is accessed by, for example, the elektormagazine.com website. IDServer needs to know where the request is coming from, to allow some different actions according to the store. Each store must be authenticated to use the IDServer (see "Stores, Authentication and Authorization" section for more information).

The `Store` resource uses the `ResourceBlueprint` trait, so you can use all methods provided by it.

```php
$store = ids()->stores(9)->get();
$allStores = ids()->stores->all();
```

**Parameters**

Parameter | Type | Required | Description
--------- | ---- | -------- | -----------
name | string | Yes | The store name, something like "Acme Store".
url | string | Yes | The store URL, like "http://acmestore.com".
mail_from | string | No | A name to be used for the "from" field in e-mails.
currencies | array | Yes | An array of all currencies that store will accept. Example: `['USD', 'EUR', 'GBP']`.

### Subscription (`subscriptions`)

A Subscription is one of the most important resource in IDServer. With the `Subscription` resource you can get information about any subscription in the API, and also manage them. Besides the `ResourceBlueprint` trait, the `Subscription` resource has two additional methods: `expiring(int $days = 7): Collection` and `renew(array $attributes): IdsEntity`.

- `expiring()`: Responsible for listing all expiring subscriptions in a given interval, in days, starting from today. For example, if you want to list all subscriptions that are going to expiring in the next 10 days (starting from today) you use:

```php
$expiringSubscriptions = ids()->subscriptions->expiring(10);
```  

- `renew()`: Responsible for renewing a given subscription. Internally, renewing a subscription is creating a new order related to it. This method requires some important parameters/attributes to be sent:

**Parameters for Renewing a Subscription**

Parameter | Type | Required | Description
--------- | ---- | -------- | -----------
currency | string| Yes | The currency related to this renewal process, like "USD" or "EUR".
user_id | integer | Yes | The user that will have the renewal associated to.
coupon | string | No | A valid coupon code, if any.
plan_duration_id | integer | Yes | The plan duration ID related to the renewal process.
store_id | integer | Yes | The store ID related to the action.
payment_code | string | Yes | The payment method code. Example: `bank_transfer`, `ideal`, `giropay`, and others. Check with the financial department for more information about them. Remembering you can manage all payment methods using the `PaymentMethod` resource.

```php
$newOrder = ids()->subscriptions(1)->renew($parameters);
```

### Tag (`tags`)

Tags are an easy way to attach metadata information to a given resource. For now only users can have tags added, using nested resource. The `Tag` resource provides you three methods for managing tags in IDServer: `create(array|string $tags)`, `update(array|string $tags)` and `all(array $filters = [])`.

```php
// Creating tags for a given user
$tagsCollection = ids()->users(1)->tags->create(['foo', 'bar']);
// Updating tags
$tagsCollection = ids()->users(1)->tags->update(['foo', 'bar']);
// Listing all tags filtering by name "ba"
$tagsCollection = ids()->users(1)->tags->all(['name' => 'ba']); // match "baz" and also "bar" for tag name
```

**Parameters**

There's is no specific parameter, only the tag name in string, that is required.

### User (`users`)

The `User` resource is the heart of the IDServer. Everything is related to a user, and that is one of the reasons the `User` resources has a huge amount of custom methods and different helpers, making it easier to work with. The resource has 14 new methods besides the ones given by the `ResourceBlueprint` trait. Below you can find an explanation of each one, plus the required parameters and usage:

- `getById()`: This is the same `get()` method from the `ResourceBlueprint` renamed to `getById()`. The `User` resource has an extra way of dealing with the `get()` method.

```php
$user = ids()->users(1)->getById();
```

- `get()`: The "users" endpoint on IDServer API also accepts multiples IDs when getting users, so that is why this method is different from others. The `get()` method on `User` resource still does not have any parameters, but it accept formatting and sending multiples IDs through the request:

```php
// Getting a collection of users which ID is 1, 4 and 6
$usersCollection = ids()->users([1, 4, 6])->get();
```

You can also use a single ID, like always:

```php
$user = ids()->users(1)->get();
```

**Custom Methods**

- `login()`: This method is responsible for log a user in the IDServer using JWT. This method has some parameters: `string $email`, `string $password`, `bool $remember = false` and `array $claims = []`. The email and password are straightforward, as always. The `$remember` parameter is used if you want to make the user logged in for 365 days (a year), and the `$claims` array is used to send extra claims to the JWT in the IDServer. For now, the IDServer only accepts the `admin_role` claim, that double check if the email is from an admin user. 

```php
// Normal user login with remember = true
$user = ids()->users->login('foo@example.com', 'secret', true);
// Admin user login
$user = ids()->users->login('admin@example.com', 'secret', true, ['admin_role' => true]);
``` 

> After the user is logged in, the JWT is stored in the session automatically, so you don't have to worry with that, or event sending it through the request. That's done automagically.

- `refreshToken(): void`: This method is used to refresh the current JWT in IDServer. If the JWT is close to expire, calling this method it will be refreshed. Usually, you don't have to deal with it, because our SDK is smart enough to identify when the JWT is expiring and then calling the `refreshToken()` method automatically to refresh the token.

```php
ids()->users->refreshToken();
```

- `confirm()`: When you create a new user it's still pending on IDServer. The response from the `create()` call gives you a temporary `$token`, that you will use to confirm the user. Only after confirmed a user is marked as valid on IDServer.

```php
$user = ids()->users(1)->confirm($token);
```

- `changeAvatar($avatar)`: This method changes the user's avatar on the IDServer. The `$avatar` parameter must be an instance of `Symfony\Component\HttpFoundation\File\UploadedFile`. Technically, the request is sent as multipart data, like you do using the browser, very magical.

```php
$avatar = new UploadedFile('/path/to/image.png', 'original_name.png');
$user = ids()->users(1)->changeAvatar($avatar);
```  

- `communications()`: Get a list of all communications a given user has. For more information about what a "communication" is take a look on the "Communication" resource section.

```php
$communications = ids()->users(1)->communications();
```

- `notes()`: Get a list of all notes the given user has. For more information about "Notes" take a look on its own section.

```php
$notes = ids()->users(1)->notes();
```

- `subscriptions()`: Get a list of all subscriptions the user has.

```php
$subscriptions = ids()->users(1)->subscriptions();
```

- `abilities()`: Get all the abilities a user has.

```php
$abilities = ids()->users(1)->abilities();
```

- `addresses()`: Get a list of all addresses the user has.

```php
$addresses = ids()->users(1)->addresses();
```

- `forgotPassword($identifier): string`: This method asks for a password change for a given user. As `$identifier` you can send the user email address or ID, both work. This method returns the temporary token as string. This token you might want to send to the user email for confirmation, with a link to your app to confirm.

```php
$token = ids()->users(1)->forgotPassword('john@example.com');
```

- `updatePassword($identifier, $token, $password): bool`: This method updates a user's password. In this case the user **is not logged in**, that's why you need a valid token. You will need the `$token` we returned in the `forgotPassword()` method call. This method returns `true` or `false` if the password was changed successfully.

```php
$isChanged = ids()->users->updatePassword('john@example.com', $token, 'new-password-123'); 
```

- `changePassword($password)`: This methods changes the user's password, but the user **must be logged in**. In this case you will need only the new password the user want's to change to. It also return a `boolean` for the result.

```php
$isChanged = ids()->users(1)->changePassword('new-password');
```

- `resetPassword()`: This methods resets the user's password and will force the user to change his password after his first login. We will send an email with the new password to the user. It also returns a boolean if everything went well.

```php
$isReset = ids()->users(1)->resetPassword();
``` 

### VatProductGroup (`vatProductGroups`)

This resource is responsible for listing all product groups from the IDServer. It has only a `all()` method, from the `ResourceBlueprint` trait. It has no valid filter.

```php
$productGroups = ids()->vatProductGroups->all();
```