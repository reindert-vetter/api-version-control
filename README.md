# API Version Control
A Laravel package to manage versions of endpoints in an elegant way.

For news, follow me on [Twitter](https://twitter.com/ReindertVetter).

[How to install](#install)

## Two ways to manage the versions of your endpoints
Option 1: **Version Statement**

 You probably use if statements to determine whether the code should be executed from a particular version (for example `if (RequestVersion::isAtLeast('2.0')) {`). But what do you do if you want to run this code for 2 endpoints, one from version 2 and the other from version 3? This package offers a clean solution for this: [Version Statement](#version-statement).

Option 2: **Version Middleware**

 Legacy code can get in the way quickly. Do you therefore create multiple controllers to separate the old code from the new code? How do you do this if there are 10 versions at a given time? By then, will you also have 10 validation schemes and response classes for each endpoint? This package also offers a SOLID solution that goes even further than *Version Statement*: [Version Middleware](#version-middleware).
> You can use *Version Middleware* and *Version Statement* together in one project

## Benefits

|    | Version Statement   |      Version Middleware      |
|----|:----------:|:-------------:|
| Upgrading all endpoints or one specific endpoint. | ✔️ | ✔️ |
| One overview of all versions with the adjustments. | ✔️ | ✔️ |
| The controller (and further) always contains the latest version. | | ✔️ |
| Old versions are only defined once. Once made, you don't have to worry about that anymore. | | ✔️ |
> Note for **Version Middleware**: If you do not yet use a self-made middleware, you can debug from your controller. With *Version Middleware*, colleagues must now understand that (only with an old version of an endpoint) the code in a middleware also influences the rest of the code.

## How To Use

### Releases
In api_version_control.php config file you will see releases with an array of versions:
```php
    'releases' => [

        'orders.index' => [
            '<=1' => [
                PrepareParameterException::class,
            ],
        ],

        'orders.store|orders.update' => [
            '<=2' => [
                ThrowCustomException::class,
                ValidateZipCode::class,
            ],
            '<=1' => [
                PrepareParameterException::class,
            ],
        ],

        'default' => [
            '<=1' => [
                ThrowCustomException::class,
            ],
        ],

    ],
```
#### Route Match
You put the route names in the key of the releases array. The key must match the current route name. Use a `|` to match multiple route names. The package runs through the route names. If a match is found, it stops searching. The match contains [Version Rules](#version_rules). If no Route Name match can be found, *default* will be used. That way you can update all your other endpoints.
> **You have to specify the route names in your router.** Example: `Route::get('orders', 'OrdersController@index')->name('orders.index');`. When using you use Resource Controllers, the names are determined automatically. For more information, see the [Laravel documentation](https://laravel.com/docs/8.x/controllers#actions-handled-by-resource-controller).

#### Version Rules
Version Rules contains a string with an operator and a version (`'<=2'`). Supported operators are: `<`, `<=`, `>`, `>=`, `==`, `!=`. All classes within the *Version Rules* with a match are used. The classes within *Version rule* are [Version Statement](#version-statement) and [Version Middleware](#version-middleware).

### Version Statement
A *Version Statement* file looks like this:
```php
<?php

namespace App\VersionControl\Orders;

use ReindertVetter\ApiVersionControl\Concerns\VersionStatement;

class ValidateZipCode
{
    use VersionStatement;
}
```
If the file contains the trait `\ReindertVetter\ApiVersionControl\Concerns\VersionStatement`, then you can do the following in your source code:
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

        return $next($request);
    }
}
```

A *Version Middleware* file (that changing the response) can looks like this:
```php
<?php

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

#### Request and Resource Binding

You can bind a FormRequest or a Resource to handle other versions. That way you can more easily support different parameters with rules, and you can more easily support different resources. A controller that supports different versions could look like:

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

The `$request` can be either OrderIndexRequestV1 or OrderIndexRequestV2 and the `$resource` can be either OrderResourceV1 or OrderResourceV2. OrderIndexRequestV2 must extend the base class OrderIndexRequest. You can do the same for the resource class. When using the `Bind` middleware, then the configuration will look like this:

```php
<?php

use ReindertVetter\ApiVersionControl\Middleware\Version\Bind;

return [

    'releases' => [

        'orders.index' => [
            '<=1' => [
                new Bind(OrderIndexRequest::class, OrderIndexRequestV1::class),
                new Bind(OrderIndexResource::class, OrderIndexResourceV1::class),
            ],
            '>=2' => [
                new Bind(OrderIndexRequest::class, OrderIndexRequestV2::class),
                new Bind(OrderIndexResource::class, OrderIndexResourceV2::class),
            ],
        ],

    ]
]
```

If it's not quite clear yet, post your question in the [discussion](https://github.com/reindert-vetter/api-version-control/discussions/8).

## Version Parser
Out of the box this package supports versions in the header accept and versions in the URI. But you can also create your own version parser. Specify this in api_version_control.php config file.

## Install
1. Run `composer require reindert-vetter/api-version-control`.
2. Add `->middleware(['api', ApiVersionControl::class])` in your `RouteServiceProvider`. If you are using URL Version Parser (which is the default) make sure the version variable is present in the url. For example:
```php
Route::middleware(['api', ApiVersionControl::class])
    ->prefix('api/{version}')
    ->where(['version' => 'v\d{1,3}'])
    ->group(base_path('routes/api.php'));
```
Now the routes are only accessible with a version in the URL (eg `/api/v2/products`). Do you also want the endpoint to work without a version in the url? Then first define the routes without the version variable:
```php
Route::middleware(['api', ApiVersionControl::class])
    ->prefix('api')
    ->as('default.')
    ->group(base_path('routes/api.php'));

Route::middleware(['api', ApiVersionControl::class])
    ->prefix('api/{version}')
    ->where(['version' => 'v\d{1,3}'])
    ->group(base_path('routes/api.php'));
```
3. Add `\ReindertVetter\ApiVersionControl\ApiVersionControlServiceProvider::class` to your providers in config/app.php
4. Create a config file by running `php artisan vendor:publish --provider='ReindertVetter\ApiVersionControl\ApiVersionControlServiceProvider'`.
5. Choose a [Version parser](#version-parser) or create one yourself.

If it's not quite clear yet, post your question in the [discussion](https://github.com/reindert-vetter/api-version-control/discussions/5).

