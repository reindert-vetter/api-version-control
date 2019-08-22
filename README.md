# API version control
A Laravel package to manage versions of endpoints in an elegant way

## Two ways to manage the versions of your endpoints
Option 1: *Version statement*

You probably use if statements to determine whether the code should be executed from a particular version (such as `if (RequestVersion::isAtLeast('2.0')) {`)? But what do you do if you want to run this code for 2 endpoints, one from version 2.0 and the other from version 3.0? This package offers a solution for this: [Version statement](version_statement).

Option 2: *Version middleware*

 Legacy code can get in the way quickly. Do you therefore create multiple controllers to separate the old code from the new code? How do you do this if there are 10 versions at a given time? By then, will you also have 10 validation schemes and response classes for each endpoint? This package also offers a solution that goes even further than _Version statement_: [Version middleware](version_middleware).
> You can use _Version middleware_ and _Version statement_ together in one project

## Benefits

|    | Version statement   |      Version middleware      |
|----|:----------:|:-------------:|
| One overview of all versions with the adjustments. | ✔️ | ✔️ |
| Upgrading all endpoints or one specific endpoint. | ✔️ | ✔️ |
|  Debug from your controller. | ✔️ | ️ |
| Use one controller, one validation and one router for one endpoint. |  | ✔️ |
| The router and the code in and from the controller always contains the latest version. | | ✔️ |
| Old versions are only defined once. You only have to worry about that once. | | ✔️ |
> Note for *Version middleware*: If you do not yet use a self-made middleware, you can debug from your controller. With _Version middleware_, colleagues must now understand that (only with an old version of an endpoint) the code in a middleware also influences the rest of the code.

## Version statement



## Version middleware
You process all requests and responses what is different from the latest version in middlewares. You can adjust the request with multiple middlewares to match the latest version. You can also adjust the format of a response in the Version middleware.

## Version parser
Out of the box this package supports versions in the header accept and versions in the URI. But you can also create your own version parser. Specify this in api_version.php config file.

## Ideas for the future
[] Generate release notes in JSON, HTML and markdown format (with description)