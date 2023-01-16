# sdpgs-gyazo

[![MIT License](http://img.shields.io/badge/license-MIT-blue.svg?style=flat)](LICENSE)
[![tests](https://github.com/tanakaphper/sdpgs-gyazo/actions/workflows/php.yml/badge.svg)](https://github.com/tanakaphper/sdpgs-gyazo/actions/workflows/php.yml)
[![MIT PHPStan](https://img.shields.io/badge/PHPStan-level%209-cornflowerblue)](https://phpstan.org/)

## Introduction

sdpgs-gyazo is a simple gyazo api client on PHP.
You can upload,get and delete image to Gyazo without any complex optional http parameters.
Only you need to do is to register your application and then generate your access token in Gyazo.
Gyazo access token is only required sdpg-gyazo parameter.
If you want to know more detailed information, please go to following official document page.

## Official Documentation

Documentation for Gyazo API can be found on [Gyazo API](https://gyazo.com/api)

## Install

```bash
composer require sdpgs/gyazo
```

## Usage

```php
$gyazoClient = GyazoClient::getInstance('{your Gyazo access token}');
/** @var string $imageBinary **/
$imageBinary = '*****';
$response = $gyazoClient->uploadImage($imageBinary, 'hoge.png');
```

## How to test

Move directory to tests/Feature and generate copy .env.example to .env.
```bash
cp .env.example .env
```

And then add GYAZO_ACCESS_TOKEN, as above-mentioned.

```bash
GYAZO_ACCESS_TOKEN={your Gyazo access token}
```

And run following composer script.

```bash
composer test
```

## Contributing

Thank you for considering to sdpgs-gyazo! But, The contribution guide doesn't get ready yet.
If you would so, please wait just a moment.

## License

sdpgs-gyazo is open-sourced sofware licensed under the MIT license.
