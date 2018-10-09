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

## Main Concepts

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

### Helper Function

To make it easier to use, the `Manager` class (the most important one) is returned from the Service Container through the PHP function `ids()` (from IDServer). Every time you need to call a resource or response in the IDServer API you only need to use `ids()` to retrieve the instance ready to go. 
