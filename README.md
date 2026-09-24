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
        <li>
            <a href="#introduction">Introduction</a>
        </li>
        <li>
            <a href="#features">Features</a>
        </li>
        <li>
            <a href="#requirements">Requirements</a>
        </li>
        <li>
            <a href="#installation">Installation</a>
            <ul>
                <li>
                    <a href="#publish-resources">Publish Resources</a>
                    <ul>
                        <li>
                            <a href="#publish-configuration">Publish Configuration</a>
                        </li>
                        <li>
                            <a href="#publish-migrations">Publish Migrations</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#run-migrations">Run Migrations</a>
                </li>
            </ul>
        </li>
        <li>
            <a href="#how-passport-works">How Passport Works</a>
        </li>
        <li>
            <a href="#usage">Usage</a>
            <ul>
                <li>
                    <a href="#application-credentials">Application Credentials</a>
                </li>
                <li>
                    <a href="#built-in-public-parameters">Built-in Public Parameters</a>
                </li>
            </ul>
        </li>
        <li>
            <a href="#request-data">Request Data</a>
            <ul>
                <li>
                    <a href="#http-headers">HTTP Headers</a>
                </li>
                <li>
                    <a href="#query-string">Query String</a>
                </li>
                <li>
                    <a href="#post-body">POST Body</a>
                </li>
                <li>
                    <a href="#combining-request-sources">Combining Request Sources</a>
                </li>
            </ul>
        </li>
        <li>
            <a href="#request-verification">Request Verification</a>
            <ul>
                <li>
                    <a href="#middleware-example">Middleware Example</a>
                </li>
            </ul>
        </li>
        <li>
            <a href="#accessing-public-parameters">Accessing Public Parameters</a>
        </li>
        <li>
            <a href="#signature-generation">Signature Generation</a>
        </li>
        <li>
            <a href="#response-signing">Response Signing</a>
        </li>
        <li>
            <a href="#custom-public-parameters">Custom Public Parameters</a>
            <ul>
                <li>
                    <a href="#custom-signature-field">Custom Signature Field</a>
                </li>
                <li>
                    <a href="#custom-parameter-list">Custom Parameter List</a>
                </li>
                <li>
                    <a href="#custom-http-header-prefix">Custom HTTP Header Prefix</a>
                </li>
                <li>
                    <a href="#custom-parameter-example">Custom Parameter Example</a>
                </li>
                <li>
                    <a href="#explicit-parameter-configuration">Explicit Parameter Configuration</a>
                </li>
            </ul>
        </li>
        <li>
            <a href="#configuration">Configuration</a>
            <ul>
                <li>
                    <a href="#disable-signature-verification">Disable Signature Verification</a>
                </li>
                <li>
                    <a href="#disable-response-signing">Disable Response Signing</a>
                </li>
            </ul>
        </li>
        <li>
            <a href="#custom-signer">Custom Signer</a>
        </li>
        <li>
            <a href="#complete-example">Complete Example</a>
            <ul>
                <li>
                    <a href="#request">Request</a>
                </li>
                <li>
                    <a href="#response">Response</a>
                </li>
                <li>
                    <a href="#custom-protocol">Custom Protocol</a>
                </li>
            </ul>
        </li>
        <li>
            <a href="#design-overview">Design Overview</a>
        </li>
        <li>
            <a href="#security-considerations">Security Considerations</a>
        </li>
        <li>
            <a href="#contributing">Contributing</a>
            <ul>
                <li>
                    <a href="#development-workflow">Development Workflow</a>
                </li>
            </ul>
        </li>
        <li>
            <a href="#contributors">Contributors</a>
        </li>
        <li>
            <a href="#license">License</a>
        </li>
    </ol>
</details>

---

## Introduction

`jundayw/passport` provides a simple and extensible way to sign and verify API requests and responses in Laravel applications.

It supports public parameters delivered through:

* HTTP request headers
* Query parameters
* Request body parameters

Passport automatically collects the configured public parameters from the request and uses them as the signing payload.

The package provides:

* Request signature verification
* Response signature generation
* Header, query, and request body support
* Built-in common parameters
* Custom signature parameter names
* Custom public parameters
* Custom HTTP header prefixes
* Pluggable signing drivers
* Laravel service container integration
* Fluent API

<p align="right">[<a href="#readme-top">back to top</a>]</p>

---

## Features

* API request signature verification
* API response signature generation
* Configurable signing algorithms
* HMAC and hash-based signing support
* Extensible custom signer implementation
* Fluent API for building and verifying signatures
* Native Laravel service provider and facade integration

<p align="right">[<a href="#readme-top">back to top</a>]</p>

---

## Requirements

* PHP 8.0 or higher
* Laravel 10.x or higher

> Check the package dependencies for the exact Laravel versions supported by the current release.

<p align="right">[<a href="#readme-top">back to top</a>]</p>

---

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

---

## How Passport Works

A typical signed API request contains a set of public parameters and a signature.

For example:

```text
app_id
action
type
charset
format
method
version
timestamp
nonce
signature
```

The `signature` field is generated from the public parameters and a shared application secret.

During verification, Passport:

1. Resolves the configured public parameters.
2. Reads them from the request.
3. Locates the signature value.
4. Removes the signature from the signing payload.
5. Resolves the application secret.
6. Generates the expected signature.
7. Compares it with the provided signature.

The signature itself is never included when generating the signature payload.

<p align="right">[<a href="#readme-top">back to top</a>]</p>

---

# Usage

## Application Credentials

Passport uses an application identifier and a shared secret to sign and verify requests.

For example:

```php
$appId = '2022082413545267';

$appSecret = 'your-app-secret';
```

The `app_id` identifies the application, while the application secret is used internally by the configured passport model to generate and verify signatures.

The application secret is not included in the request.

## Built-in Public Parameters

Laravel Passport 6.0.0 provides the following public parameters by default:

```php
[
    'app_id',
    'action',
    'type',
    'charset',
    'format',
    'method',
    'version',
    'timestamp',
    'nonce',
]
```

These parameters are used to identify the application and describe the request when generating or verifying a signature.

| Parameter   | Description                                                |
|-------------|------------------------------------------------------------|
| `app_id`    | Application identifier used to resolve the signing secret. |
| `action`    | API action or business operation.                          |
| `type`      | Signature algorithm, such as `MD5` or `SHA512`.            |
| `charset`   | Character encoding used by the request.                    |
| `format`    | Request or response data format, such as `JSON`.           |
| `method`    | HTTP request method, such as `GET` or `POST`.              |
| `version`   | API version.                                               |
| `timestamp` | Request timestamp.                                         |
| `nonce`     | Unique request value used to distinguish requests.         |

The signature field is automatically appended to the parameter list. Its default name is `signature`.

> The public parameters are configurable. The actual signing algorithm, canonicalization rules, and secret resolution are determined by the configured signing driver and application implementation.

<p align="right">[<a href="#readme-top">back to top</a>]</p>

---

# Request Data

Passport supports the following request data sources:

* HTTP headers
* Query parameters
* Request body parameters

You can explicitly provide the data to be used during signature verification.

## HTTP Headers

When the public parameters are transmitted through HTTP headers, the default `x` prefix converts parameter names into HTTP header names.

For example:

| Parameter   | HTTP Header   |
|-------------|---------------|
| `app_id`    | `X-App-Id`    |
| `action`    | `X-Action`    |
| `type`      | `X-Type`      |
| `charset`   | `X-Charset`   |
| `format`    | `X-Format`    |
| `method`    | `X-Method`    |
| `version`   | `X-Version`   |
| `timestamp` | `X-Timestamp` |
| `nonce`     | `X-Nonce`     |
| `signature` | `X-Signature` |

An example request header set is:

```http
X-Action: api
X-App-Id: 2022082413545267
X-Charset: UTF-8
X-Format: JSON
X-Method: POST
X-Nonce: DB17CF80-F0E2-4B7D-B075-6BDE33EA03DD
X-Signature: 2BEEC5BA673C4BBF04649D36EC460998D542BF68527E6DA903A622069FE4F4A7AB7F342143454B3EF13615C59CC494454A27E32C06D5C7B389CC74AD845A32D0
X-Timestamp: 2026-09-24T11:39:48.165Z
X-Type: SHA512
X-Version: 1.0.0
```

You can read the header values and verify the request as follows:

```php
$passport = Passport::query($request->query())
    ->request($request->post());

$verified = $passport->verify(
    $passport->getParameter('app_id'),
    $passport->getParameter('type'),
);
```

The `Passport::header()` method accepts the application's header data. The parameter names used by the signing process remain consistent with the configured public parameters.

## Query String

Public parameters can also be sent through the URL query string.

Example:

```text
/api?app_id=2022082413545267&action=api&type=SHA512&charset=UTF-8&format=JSON&method=POST&version=1.0.0&timestamp=2026-09-24T11:40:56.350Z&nonce=1C93F1BA-5330-4365-97DF-4D44108994E7&signature=04B7973BB74B0250A391825F8D261635BC68564A9C90C9D896D2DAC11A99B2B4A68512139343103E7A5199A33BE96A3C655505320C3F754FD6D6D4E648EED945
```

Verification example:

```php
$passport = Passport::query($request->query());

$verified = $passport->verify(
    $passport->getParameter('app_id'),
    $passport->getParameter('type'),
);
```

## POST Body

When the public parameters and signature are sent together in a POST request body, the payload may look like this:

```json
{
    "app_id": "2022082413545267",
    "action": "api",
    "type": "SHA512",
    "charset": "UTF-8",
    "format": "JSON",
    "method": "POST",
    "version": "1.0.0",
    "timestamp": "2026-09-24T11:38:17.327Z",
    "nonce": "D06AF494-95B8-48EB-B6C5-128980F42C40",
    "signature": "2FF555B4DBB65AD9F96A3F11B311FBE1EA7D6B424768EAF06E71C37179704A352781C3ADAD7F45C158DC5CEBCC68E68C8234AFB38F93FC9E9780ED3A1AD4189D"
}
```

The request can be verified using:

```php
$passport = Passport::request($request->post());

$verified = $passport->verify(
    $passport->getParameter('app_id'),
    $passport->getParameter('type'),
);
```

## Combining Request Sources

Passport allows request data to be assembled from multiple locations.

For example:

```php
$passport = Passport::query($request->query())
    ->request($request->post());
```

This is useful when an API protocol defines different parts of the request in different locations.

For example:

```text
HTTP Header
    ↓
X-App-Id
X-Type
X-Timestamp
X-Nonce

Query String
    ↓
page
limit

Request Body
    ↓
order_id
amount
```

Passport keeps these sections separately:

```php
[
    'header' => [...],
    'params' => [...],
    'data'   => [...],
]
```

This allows the signing implementation to preserve the original request structure while generating a normalized signing payload.

<p align="right">[<a href="#readme-top">back to top</a>]</p>

---

# Request Verification

The recommended way to verify a request is to build a Passport instance from the request data:

```php
use Jundayw\Passport\Facades\Passport;

$passport = Passport::query($request->query())
    ->request($request->post());

if ($passport->verify(
    $passport->getParameter('app_id'),
    $passport->getParameter('type'),
)) {
    return $next($request);
}
```

The second argument determines the hashing algorithm.

For example:

```php
$passport->verify(
    '2022082413545267',
    'SHA512',
);
```

The third argument can be used to select a signing driver:

```php
$passport->verify(
    '2022082413545267',
    'SHA512',
    'hash_hmac',
);
```

The default driver is:

```text
hash_hmac
```

## Middleware Example

Passport can collect request parameters and verify the signature through the `Passport` facade.

The following example uses a Laravel middleware to verify incoming API requests.

```php
<?php

namespace App\Http\Middleware;

use App\Exceptions\InvalidSignatureException;
use Closure;
use Illuminate\Http\Request;
use Jundayw\Passport\Facades\Passport;

class ValidateSignature
{
    public function handle(Request $request, Closure $next)
    {
        $passport = Passport::query($request->query())
            ->request($request->post());

        if ($passport->verify(
            $passport->getParameter('app_id', '2022082413545267'),
            $passport->getParameter('type', 'md5'),
        )) {
            return $next($request);
        }

        $this->processInvalidSignature($request);
    }

    protected function processInvalidSignature(Request $request)
    {
        throw new InvalidSignatureException;
    }
}
```

The middleware performs the following operations:

1. Creates a passport instance through the facade.
2. Adds query parameters using `query()`.
3. Adds POST parameters using `request()`.
4. Retrieves the application identifier and signature algorithm.
5. Verifies the request signature.
6. Continues the request pipeline when verification succeeds.
7. Throws an application-specific exception when verification fails.

Register the middleware in the appropriate middleware group or route definition.

> The fallback application identifier and algorithm in this example are for demonstration only. In production, use values appropriate to your application's authentication and error-handling requirements.

<p align="right">[<a href="#readme-top">back to top</a>]</p>

---

# Accessing Public Parameters

You can retrieve a resolved parameter using:

```php
$passport->getParameter('app_id');
```

A default value can also be provided:

```php
$passport->getParameter('app_id', '2022082413545267');
```

Retrieve all resolved parameters:

```php
$passport->getParameters();
```

Retrieve the signature:

```php
$passport->getSignatureValue();
```

For example:

```php
$appId = $passport->getParameter('app_id');

$type = $passport->getParameter('type');

$signature = $passport->getSignatureValue();
```

<p align="right">[<a href="#readme-top">back to top</a>]</p>

---

# Signature Generation

Passport can generate a signature from the current passport data:

```php
$signature = $passport->signature(
    $passport->getParameter('app_id'),
    $passport->getParameter('type'),
);
```

For example:

```php
$passport = Passport::query($request->query())
    ->request($request->post());

$signature = $passport->signature(
    $passport->getParameter('app_id'),
    $passport->getParameter('type'),
);
```

The signature parameter is automatically excluded from the signing payload.

<p align="right">[<a href="#readme-top">back to top</a>]</p>

---

# Response Signing

Passport can also sign response data.

Create a response passport:

```php
$passport = Passport::response([
    'foo' => 'bar',
]);
```

Generate the signature:

```php
$signature = $passport->signature(
    $passport->getParameter('app_id'),
    $passport->getParameter('type'),
);
```

Alternatively, use `withSignature()` to append the generated signature:

```php
$response = Passport::response([
    'foo' => 'bar',
])->withSignature(
    $appId,
    'SHA512',
)->getResponse();
```

The resulting data contains the signature:

```php
[
    'foo' => 'bar',
    'signature' => '...',
]
```

<p align="right">[<a href="#readme-top">back to top</a>]</p>

---

# Custom Public Parameters

You can customize the signature field name, public parameter list, and HTTP header prefix using `Passport::make()`.

```php
use Jundayw\Passport\Facades\Passport;

$passport = Passport::make(
    'sign',
    [
        'key',
        'hash',
        'nonce',
    ],
    't',
);
```

The arguments are:

```php
Passport::make(
    string $signatureKey = 'signature',
    array $params = [],
    string|null $prefix = 'x',
);
```

| Argument        | Description                                                         |
|-----------------|---------------------------------------------------------------------|
| `$signatureKey` | Custom signature field name.                                        |
| `$params`       | Custom public parameter names.                                      |
| `$prefix`       | Prefix used when converting public parameters to HTTP header names. |

## Custom Signature Field

The first argument defines the signature field name.

```php
$passport = Passport::make('sign');
```

The signature field is now:

```text
sign
```

Instead of the default:

```text
signature
```

## Custom Parameter List

The second argument defines the public parameters used by the passport instance.

```php
$passport = Passport::make(
    'sign',
    [
        'key',
        'hash',
        'nonce',
    ],
);
```

The configured public parameters are:

```php
[
    'key',
    'hash',
    'nonce',
]
```

The custom signature field `sign` is automatically included in the parameter list.

## Custom HTTP Header Prefix

The third argument defines the prefix used for HTTP header names.

```php
$passport = Passport::make(
    'sign',
    [
        'app_id',
        'action',
    ],
    't',
);
```

With the `t` prefix, the parameter `app_id` is converted to:

```http
T-App-Id
```

The parameter `action` is converted to:

```http
T-Action
```

To disable the prefix, pass `null`:

```php
$passport = Passport::make(
    'sign',
    [
        'app_id',
        'action',
    ],
    null,
);
```

The corresponding header names will be:

```http
App-Id
Action
```

## Custom Parameter Example

For an API using the following parameters:

```php
[
    'client_id',
    'request_id',
    'algorithm',
    'timestamp',
]
```

with a custom signature field:

```text
sign
```

and a custom header prefix:

```text
api
```

you can configure Passport as follows:

```php
$passport = Passport::make(
    'sign',
    [
        'client_id',
        'request_id',
        'algorithm',
        'timestamp',
    ],
    'api',
);
```

The corresponding HTTP headers become:

```text
Api-Client-Id
Api-Request-Id
Api-Algorithm
Api-Timestamp
Api-Sign
```

## Explicit Parameter Configuration

If you need to override the default parameter configuration for a particular request, use `parameters()`:

```php
$passport = Passport::parameters([
    'app_id',
    'action',
    'type',
    'charset',
    'format',
    'method',
    'version',
    'timestamp',
    'nonce',
    'signature',
]);
```

This is useful when a specific API uses a parameter set different from the application's default.

The normal fluent API can then be used:

```php
$passport = Passport::parameters([
    'app_id',
    'action',
    'type',
    'timestamp',
    'nonce',
    'signature',
])->query($request->query())
    ->request($request->post());
```

In most cases, however, the built-in parameters are sufficient and manual configuration is unnecessary.

<p align="right">[<a href="#readme-top">back to top</a>]</p>

---

# Configuration

Passport supports configuration options for controlling request verification and response signing.

## Disable Signature Verification

Passport supports disabling request verification through configuration.

When request verification is disabled, `verify()` returns `true` without performing signature verification.

This can be useful for development, testing, or environments where signature verification is handled externally.

The corresponding configuration options are:

```php
'passport.enabled'
'passport.ignore.request'
```

## Disable Response Signing

Response signing can similarly be controlled with:

```php
'passport.ignore.response'
```

<p align="right">[<a href="#readme-top">back to top</a>]</p>

---

# Custom Signer

Passport uses a driver-based signing architecture.

The default driver is:

```text
hash_hmac
```

You can register your own signing driver by implementing the `Signer` contract.

```php
use Jundayw\Passport\Contracts\Signer;
use Jundayw\Passport\Facades\Passport;

Passport::extend('AES', function () {
    return new class implements Signer {
        public function sign(
            string $algo,
            array $data,
            string $secret,
        ): string {
            // Implement your signing algorithm.
        }

        public function verify(
            string $algo,
            array $data,
            string $secret,
            string $signatureValue,
        ): bool {
            // Implement your verification algorithm.
        }
    };
});
```

The custom driver can then be selected when verifying a request:

```php
$passport->verify(
    $appId,
    'AES-256-CBC',
    'AES',
);
```

Or when generating a signature:

```php
$passport->signature(
    $appId,
    'AES-256-CBC',
    'AES',
);
```

The third argument identifies the registered driver:

```php
Passport::extend('AES', ...);
```

This allows application-specific signing strategies to be added without modifying Passport's core implementation.

<p align="right">[<a href="#readme-top">back to top</a>]</p>

---

# Complete Example

A typical Laravel application can use Passport as follows.

## Request

```php
use Illuminate\Http\Request;
use Jundayw\Passport\Facades\Passport;

$passport = Passport::query($request->query())
    ->request($request->post());

if (! $passport->verify(
    $passport->getParameter('app_id'),
    $passport->getParameter('type'),
)) {
    throw new InvalidSignatureException;
}
```

## Response

```php
$passport = Passport::response([
    'code' => 200,
    'data' => [
        'id' => 1001,
    ],
]);

$response = $passport->withSignature(
    $passport->getParameter('app_id'),
    $passport->getParameter('type'),
)->getResponse();
```

## Custom Protocol

If your API uses:

```text
signature field: sign
public parameters: client_id, timestamp, nonce
header prefix: api
```

configure it with:

```php
$passport = Passport::make(
    'sign',
    [
        'client_id',
        'timestamp',
        'nonce',
    ],
    'api',
);
```

The corresponding headers are:

```text
Api-Client-Id
Api-Timestamp
Api-Nonce
Api-Sign
```

<p align="right">[<a href="#readme-top">back to top</a>]</p>

---

# Design Overview

Passport separates request data collection, parameter resolution, signature generation, and credential resolution.

```text
                    Laravel Request
                          │
          ┌───────────────┼───────────────┐
          ▼               ▼               ▼
       Headers          Query           Body
          │               │               │
          └───────────────┼───────────────┘
                          ▼
                       Passport
                          │
                    Public Parameters
                          │
                          ▼
                    Remove Signature
                          │
                          ▼
                     Sign / Verify
                          │
              ┌───────────┴───────────┐
              ▼                       ▼
          Request                 Response
           Verify                    Sign
```

This architecture allows Passport to support different API protocols without coupling the signing logic to a specific request transport.

<p align="right">[<a href="#readme-top">back to top</a>]</p>

---

# Security Considerations

When implementing request signing, consider the following:

* Keep application secrets private and never include them in requests.
* Validate timestamps to prevent replay attacks.
* Use a unique nonce for each request.
* Ensure the signature algorithm is agreed upon by both client and server.
* Use HTTPS to protect request data in transit.
* Keep the parameter list and canonicalization rules consistent between signing and verification.
* Store application secrets in a secure credential provider.
* Handle invalid signatures through application-specific exceptions and error responses.

<p align="right">[<a href="#readme-top">back to top</a>]</p>

---

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

---

## Contributors

Thanks to all the people who have contributed to this project.

<a href="https://github.com/jundayw/laravel-passport/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=jundayw/laravel-passport" alt="Contributors" />
</a>

Contributions of any kind are welcome!

<p align="right">[<a href="#readme-top">back to top</a>]</p>

---

## License

Distributed under the MIT License. See the [License File][License File] for more information.

<p align="right">[<a href="#readme-top">back to top</a>]</p>

---

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
