<a id="readme-top"></a>

# Laravel Passport

A lightweight and extensible Laravel package for API request verification and response signing.

[![GitHub Tag][GitHub Tag]][GitHub Tag URL]
[![Total Downloads][Total Downloads]][Packagist URL]
[![Packagist Version][Packagist Version]][Packagist URL]
[![Packagist PHP Version Support][Packagist PHP Version Support]][Packagist URL]
[![Packagist License][Packagist License]][Repository URL]

<!-- TABLE OF CONTENTS -->

<details>
    <summary>Table of Contents</summary>
    <ol>
        <li><a href="#features">Features</a></li>
        <li><a href="#requirements">Requirements</a></li>
        <li><a href="#installation">Installation</a></li>
        <li>
            <a href="#usage">Usage</a>
            <ul>
                <li><a href="#configuration">Configuration</a></li>
                <li><a href="#request-verification">Request Verification</a></li>
                <li><a href="#response-signing">Response Signing</a></li>
                <li><a href="#custom-signer">Custom Signer</a></li>
            </ul>
        </li>
        <li><a href="#contributing">Contributing</a></li>
        <li><a href="#contributors">Contributors</a></li>
        <li><a href="#license">License</a></li>
    </ol>
</details>

<!-- FEATURES -->

## Features

* API request signature verification
* API response signature generation
* Configurable signing algorithms
* HMAC and hash-based signing support
* Extensible custom signer implementation
* Fluent API for building and verifying signatures
* Native Laravel service provider and facade integration

<p align="right">[<a href="#readme-top">back to top</a>]</p>

<!-- REQUIREMENTS -->

## Requirements

* PHP 8.0 or higher
* Laravel 10.x or higher

> Check the package dependencies for the exact Laravel versions supported by the current release.

<p align="right">[<a href="#readme-top">back to top</a>]</p>

<!-- INSTALLATION -->

## Installation

Install the package via Composer:

```bash
composer require jundayw/passport
```

### Publish Resources

Publish the package resources using the service provider:

```bash
php artisan vendor:publish --provider="Jundayw\Passport\PassportServiceProvider"
```

Or publish specific resources using their corresponding tags.

#### Publish Configuration

```bash
php artisan vendor:publish --tag=passport-config
```

#### Publish Migrations

```bash
php artisan vendor:publish --tag=passport-migrations
```

### Run Migrations

After publishing the migrations, run:

```bash
php artisan migrate --path=database/migrations/2026_03_01_000000_create_passport_table.php
```

<p align="right">[<a href="#readme-top">back to top</a>]</p>

<!-- USAGE -->

## Usage

### Configuration

Define the application credentials used for signing and verification:

```php
$appId     = '202603161735';
$appSecret = '2f7b50c39cb5f4cf061b0ea433634287';
```

The `$appId` identifies the application, while `$appSecret` is the shared secret used to generate and verify signatures.

<p align="right">[<a href="#readme-top">back to top</a>]</p>

### Request Verification

Use the `Passport` facade to build a request verification instance:

```php
use Jundayw\Passport\Facades\Passport;

$passport = Passport::query([
    'foo' => 'bar',
]);

$passport->header($request->header());
$passport->query($request->query());
$passport->request($request->post());

$passport->check(
    $appId,
    'sha256',
    'signature',
    'hash_hmac'
); // true
```

The request payload can also be provided explicitly:

```php
$passport = Passport::request([
    'foo'       => 'bar',
    'signature' => '51864429c137b125833e8969649e8371a97b61af875ddd09366676e7df236966',
]);

$passport->check(
    $appId,
    'sha256',
    'signature',
    'hash_hmac'
); // true
```

The verification process supports different request components, including:

* Request headers
* Query parameters
* Request body parameters

These values can be combined before calculating the signature.

<p align="right">[<a href="#readme-top">back to top</a>]</p>

### Response Signing

Use `Passport::response()` to create a response signing instance:

```php
use Jundayw\Passport\Facades\Passport;

$passport = Passport::response([
    'foo' => 'bar',
]);

$signature = $passport->signature(
    $appId,
    'sha256',
    'signature',
    'hash_hmac'
);

// 51864429c137b125833e8969649e8371a97b61af875ddd09366676e7df236966
```

To append the generated signature directly to the response:

```php
$response = Passport::response([
    'foo' => 'bar',
])->withSignature(
    $appId,
    'sha256',
    'signature',
    'hash_hmac'
)->getResponse();
```

The resulting response will be:

```php
[
    'foo'       => 'bar',
    'signature' => '51864429c137b125833e8969649e8371a97b61af875ddd09366676e7df236966',
]
```

<p align="right">[<a href="#readme-top">back to top</a>]</p>

### Custom Signer

You can extend Passport with your own signing algorithm by implementing the `Signer` contract:

```php
use Jundayw\Passport\Contracts\Signer;
use Jundayw\Passport\Facades\Passport;

Passport::extend('AES', function () {
    return new class implements Signer {
        public function sign(
            string $algo,
            array $data,
            string $secret
        ): string {
            // Implement your signing algorithm.
        }

        public function verify(
            string $algo,
            array $data,
            string $signatureValue,
            string $secret
        ): bool {
            // Implement your verification algorithm.
        }
    };
});
```

The custom signer can then be used when verifying a request:

```php
$passport = Passport::request($request->post());

$passport->check(
    $appId,
    'AES-256-CBC',
    'signature',
    'AES'
); // bool
```

The fourth argument identifies the registered signer:

```php
Passport::extend('AES', ...);
```

This allows the package to support application-specific signing and verification strategies without modifying the core implementation.

<p align="right">[<a href="#readme-top">back to top</a>]</p>

<!-- CONTRIBUTING -->

## Contributing

Contributions are welcome and greatly appreciated.

If you have an idea, improvement, or bug fix, feel free to open an issue or submit a pull request.

### Development Workflow

1. Fork the project.

2. Create your feature branch:

   ```bash
   git checkout -b feature/AmazingFeature
   ```

3. Commit your changes:

   ```bash
   git commit -m "Add some AmazingFeature"
   ```

4. Push your branch:

   ```bash
   git push origin feature/AmazingFeature
   ```

5. Open a Pull Request.

If you find the project useful, consider giving it a star on GitHub.

<p align="right">[<a href="#readme-top">back to top</a>]</p>

<!-- CONTRIBUTORS -->

## Contributors

Thanks to all the people who have contributed to this project.

<a href="https://github.com/jundayw/laravel-passport/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=jundayw/laravel-passport" alt="Contributors" />
</a>

Contributions of any kind are welcome!

<p align="right">[<a href="#readme-top">back to top</a>]</p>

<!-- LICENSE -->

## License

Distributed under the MIT License. See the [License File] for more information.

<p align="right">[<a href="#readme-top">back to top</a>]</p>

[GitHub Tag]: https://img.shields.io/github/v/tag/jundayw/laravel-passport

[Total Downloads]: https://img.shields.io/packagist/dt/jundayw/passport?style=flat-square

[Packagist Version]: https://img.shields.io/packagist/v/jundayw/passport

[Packagist PHP Version Support]: https://img.shields.io/packagist/php-v/jundayw/passport

[Packagist License]: https://img.shields.io/github/license/jundayw/laravel-passport

[GitHub Tag URL]: https://github.com/jundayw/laravel-passport/tags

[Packagist URL]: https://packagist.org/packages/jundayw/passport

[Repository URL]: https://github.com/jundayw/laravel-passport

[Composer]: https://getcomposer.org

[License File]: https://github.com/jundayw/laravel-passport/blob/main/LICENSE
