# API version control
A Laravel package to manage versions of endpoints in an elegant way

## Two ways to manage the versions of your endpoints
Option 1: **Version Statement**

You probably use if statements to determine whether the code should be executed from a particular version (for example `if (RequestVersion::isAtLeast('2.0')) {`). But what do you do if you want to run this code for 2 endpoints, one from version 2.0 and the other from version 3.0? This package offers a clean solution for this: [Version statement](#version-statement).

Option 2: **Version Middleware**

 Legacy code can get in the way quickly. Do you therefore create multiple controllers to separate the old code from the new code? How do you do this if there are 10 versions at a given time? By then, will you also have 10 validation schemes and response classes for each endpoint? This package also offers a SOLID solution that goes even further than *Version statement*: [Version middleware](#version-middleware).
> You can use *Version middleware* and *Version statement* together in one project

## Benefits

|    | Version Statement   |      Version Middleware      |
|----|:----------:|:-------------:|
| Upgrading all endpoints or one specific endpoint. | ✔️ | ✔️ |
| One overview of all versions with the adjustments. | ✔️ | ✔️ |
| The controller (and further) always contains the latest version. | | ✔️ |
| Old versions are only defined once. Once made, you don't have to worry about that anymore. | | ✔️ |
> Note for **Version middleware**: If you do not yet use a self-made middleware, you can debug from your controller. With *Version middleware*, colleagues must now understand that (only with an old version of an endpoint) the code in a middleware also influences the rest of the code.

## How to use

### Releases
In api_version_control.php config file you will see releases with an array of versions:
```php
    'releases' => [

        'GET/orders' => [
            '<=1.0' => [
                PrepareParameterException::class,
            ],
        ],

        '(POST|PUT)/orders' => [
            '<=2.0' => [
                ThrowCustomException::class,
                ValidateZipCode::class,
            ],
            '<=1.0' => [
                PrepareParameterException::class,
            ],
        ],

        'default' => [
            '<=1.0' => [
                ThrowCustomException::class,
            ],
        ],

    ],
```
#### URI match
The URI match contains a string to match the endpoint with regex (`GET/orders`). The subject contains the method and the URI. It runs through the version rules. If a match is found, it stops searching. The match contains [Version rules](#version_rules).
> If no URI match can be found, *default* will be used. That way you can update all your other endpoints.

#### Version rule
Version rules contains a string with an operator and a version (`'<=2.0'`). Supported operators are: `<`, `<=`, `>`, `>=`, `==`, `!=`. All classes within the *Version rules* with a match are used. The classes within *Version rule* are [Version statement](#version-statement) and [Version middleware](#version-middleware).

### Version Statement
A *Version Statement* file looks like this:
```php
<?php
declare(strict_types=1);

namespace App\VersionControl\Orders;

use ReindertVetter\ApiVersionControl\Concerns\VersionStatement;

class ValidateZipCode
{
    use VersionStatement;
}
```
If the file contains the trait `ReindertVetter\ApiVersionControl\Concerns\VersionStatement`, then you can do the following in your source code:
```php
if (ValidateZipCode::permitted()) {
    (...)
}
```

### Version Middleware
You process all requests and responses what is different from the latest version in middlewares. You can adjust the request with multiple middlewares to match the latest version. You can also adjust the format of a response in the Version Middleware.

A *Version Middleware* file (that changing the request) can looks like this:
```php
<?php
declare(strict_types=1);

namespace App\Middleware\Version;

use Closure;
use Illuminate\Http\Request;

class PrepareParameterException
{
    /**
     * @param           $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Set the default parameter because it is required in a newer version.
        $request->query->set('sort', 'DESC');

        /** @var \Illuminate\Http\Response $response */
        $response = $next($request);

        return $response;
    }
}
```

A *Version Middleware* file (that changing the response) can looks like this:
```php
<?php
declare(strict_types=1);

namespace App\Middleware\Version;

use Closure;
use Illuminate\Http\Request;

class ThrowHumanException
{
    /**
     * @param           $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var \Illuminate\Http\Response $response */
        $response = $next($request);

        // Catch the exception to return an exception in a different format.
        if ($response->exception) {
            $response->setContent(
                [
                    "errors" => [
                        [
                            "human" => $response->exception->getMessage(),
                        ],
                    ],
                ]
            );
        }

        return $response;
    }
}
```

#### Request and Resource binding

In a Version Middleware you can use Laravel Binding. So you can bind a FormRequest or a Resource to handle other versions. That way you can more easily support different parameters with rules and you can more easily support different resources. A controller that supports different versions could look like:

```php
    public function index(OrderIndexRequest $request, OrderResource $resource): ResourceCollection
    {
        $orders = Order::query()
            ->productIds($request->productIds())
            ->with($resource->withRelationships())
            ->paginate($request->limit());

        return $resource::collection($orders);
    }
```

The `$request` can be either OrderIndexRequestV1 or OrderIndexRequestV2 and the `$resource` can be either OrderResourceV1 or OrderResourceV2.

You have to create a BindOrderIndexRequestV1 and a BindOrderIndexRequestV2 middleware in which you will bind the request:
```php
class BindOrderIndexRequestV2
{
    public function handle(Request $request, Closure $next)
    {
        app()->bind(OrderIndexRequest::class, OrderIndexRequestV2::class);

        return $next($request);
    }
}
```

The requests must extend the base class OrderIndexRequest. You can do the same for the resource class. The configuration will then look like this:
```php
        '(GET)/orders' => [
            '<=1' => [
                BindOrderIndexRequestV1::class,
                BindOrderResourceV1::class,
            ],
            '>=2' => [
                BindOrderIndexRequestV2::class,
                BindOrderResourceV2::class,
            ],
        ],
```

If it's not quite clear yet, post your question in the [discussion](https://github.com/reindert-vetter/api-version-control/discussions/5).

## Version parser
Out of the box this package supports versions in the header accept and versions in the URI. But you can also create your own version parser. Specify this in api_version_control.php config file.

## Install
1. Run `composer require reindert-vetter/api-version-control`.
1. Add `->middleware(['api', ApiVersionControl::class])` in your `RouteServiceProvider`.
1. Create config file by running `php artisan vendor:publish --provider='ReindertVetter\ApiVersionControl\ApiVersionControlServiceProvider'`.
1. Choose a [Version parser](#version-parser) or create one yourself.

