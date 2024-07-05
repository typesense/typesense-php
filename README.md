# Typesense PHP Client

Official PHP client for the Typesense API: https://github.com/typesense/typesense

## Installation

```
$ composer require php-http/curl-client typesense/typesense-php
```

You can also add `typesense/typesense-php` to your project's `composer.json`.

Typesense uses [HTTPlug](http://httplug.io/) as an abstraction layer for an HTTP client. You'll find the List of supported HTTP clients & adapters [here](http://docs.php-http.org/en/latest/clients.html). Please be sure to install a supported client.

## Usage

Read the documentation here: [https://typesense.org/api/](https://typesense.org/api/)

Here are some examples that walk you through how to use the client: [doc/examples](examples)

## Compatibility

| Typesense Server | typesense-php |
|------------------|---------------|
| \>= v26.0        | \>= v4.9.0    |
| \>= v0.23.0      | \>= v4.8.0    |
| \>= v0.21.0      | \>= v4.7.0    |
| \>= v0.20.0      | \>= v4.6.0    |
| \>= v0.19.0      | \>= v4.5.0    |
| \>= v0.18.0      | \>= v4.4.0    |
| \>= v0.17.0      | \>= v4.2.0    |
| \>= v0.16.0      | \>= v4.1.0    |
| \>= v0.15.0      | \>= v4.0.0    |

## Contributing

Bug reports and pull requests are welcome on GitHub at [https://github.com/typesense/typesense-php].

## Development

Run linter:

```shell script
composer run-script lint:fix
```

Run Typesense Server:

```shell script
composer run-script typesenseServer
```

Run tests:

```shell script
docker compose up
cp phpunit.xml.dist phpunit.xml
composer run-script test
```

## Credits

This client was originally developed by [Abdullah Al-Faqeir](https://github.org/abdullahfaqeir) from 
[DevLoops](https://github.com/devloopsnet) and was 
[adopted](https://github.com/devloopsnet/typesense-php/issues/4) as the official PHP client library for Typesense in Oct 2020.

Ongoing development and support is now provided by Typesense, in addition to our collaborators.
