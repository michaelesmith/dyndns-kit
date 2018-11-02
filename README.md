[![Build Status](https://travis-ci.org/michaelesmith/dyndns-kit.svg?branch=master)](https://travis-ci.org/michaelesmith/dyndns-kit)

# What is this?
This is a framework that will allow you to easily create and use a simple DynDNS implementation in PHP while being easily hackable to add new features and processors. This project was born out of my frustration in trying to find a simple PHP DynDNS implementation that would allow me to use Digital Ocean as the backend DNS. This project solved my problem, hopefully it will be useful to someone else.

# Install
`composer require "michaelesmith/dyndns-kit"`

# Overview
`Server`: basic unit that will try to execute a request
  - `Handler`: operates on a specific type of request. A generic handler for typical dyndns requests is provided but you could create one to handle API request for instance.
    - `Transformer`: this transforms a raw request into a `Query`
    - `Authenicator`: this allows us to authenticate the request based on many factors. The included authenticator will use an http basic username and password and can limit users to specific domains.
    - `Processor`: this processes our given query and stores the result.

# How do I use it
To see an example usage please refer to the [example project](https://github.com/michaelesmith/dyndns-example). 

## Basic usage
```php
$server = new Server([
    new GenericHandler(
        new DynDNSTransformer(),
        new HttpBasicAuthenticator([new RegexUser('testuser', 's3cret', '.+')]),
        new JsonProcessor(__DIR__ . '/var/dns.json')
    ),
]);
$server->execute(Request::createFromGlobals());
```

In this example we use only a single `GenericHandler`, the standard `DynDNSTransformer`, the `HttpBasicAuthenticator` with a single user that will allow any domain and the included `JsonProcessor`. The `JsonProcessor` is probably not very useful in real life but it allows us to easily test this setup without the need for other components. Using the curl example below will create the file dns.json and store the address in it.

## More advanced usage
```php
$server = new Server([
    new GenericHandler(
        new DynDNSTransformer(),
        new HttpBasicAuthenticator([new RegexUser('testuser', 's3cret', '.+')]),
        new CacheProcessor(
            new DigitalOceanApiProcessor(['example.com'], new DigitalOceanV2(new GuzzleHttpAdapter('my_api_token'))),
            new FilesystemCachePool(new Filesystem(new Local(__DIR__ . '/../var/')))
        )
    ),
]);
$server->execute(Request::createFromGlobals());
```

This example uses the exact same setup as before except for the processor. Here we use a caching processor so only new or updated requests are actually sent to the embedded processor which in this case updates the DNS entry for a domain hosted at Digital Ocean.

# Try it out
Example curl command

`curl "testuser:s3cret@localhost:9001/nic/update?hostname=test.example.com&myip=127.0.0.1"`

Example with multiple hosts

`curl "testuser:s3cret@localhost:9001/nic/update?hostname=test1.example.com,test2.example.com&myip=127.0.0.1"`

Example without IP (if no IP is given the client IP address will be used)

`curl "testuser:s3cret@localhost:9001/nic/update?hostname=test.example.com"`

# Other processors
* [Cache](https://github.com/michaelesmith/dyndns-processor-cache) - allows an embedded processor to only be called with new or updated requests
* [DigitalOcean](https://github.com/michaelesmith/dyndns-processor-digitalocean) - allows the you to update dns entries via the Digital Ocean API

# Contributing
Have an idea to make something better? Submit a pull request. Need integration of some other backend service? Build it. I would be happy to add a link here. PR's make the open source world turn. :earth_americas: :earth_asia: :earth_africa: :octocat: Happy Coding!
