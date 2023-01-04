Transistor API
=============

Super-simple, minimum abstraction Transistor API wrapper, in PHP.

I hate complex wrappers. This lets you get from the Transistor API docs to the code as directly as possible.

Requires PHP 7.4+ and a pulse.

Installation
------------

You will be able to install transistor-api using Composer, once it's ready.

<!--

DRAFT

This is a bunch of stuff that will become relevant once the API wrapper is complete.

```
composer require createwithrani/mailchimp-api
```

You will then need to:
* run ``composer install`` to get these dependencies added to your vendor directory
* add the autoloader to your application with this line: ``require("vendor/autoload.php")``

Alternatively you can just download the `Transistor.php` file and include it manually:

```php
include('./Transistor.php');
```

/END NOT READY
* * *

Examples
--------

Start by `use`-ing the class and creating an instance with your API key

```php
use \Aurooba\Transistor\Transistor;

$Transistor = new Transistor('abc123abc123abc123abc123abc123-us1');
```


Troubleshooting
---------------

To get the last error returned by either the HTTP client or by the API, use `get_last_error()`:

```php
echo $Transistor->get_last_error();
```

For further debugging, you can inspect the headers and body of the response:

```php
print_r($Transistor->get_last_response());
```

If you suspect you're sending data in the wrong format, you can look at what was sent to Transistor by the wrapper:

```php
print_r($Transistor->get_last_request());
```

Contributing
------------

This is a pretty simple wrapper. If you'd like to suggest an improvement, please raise an issue to discuss it before making your pull request.

Pull requests for bugs are more than welcome - please explain the bug you're trying to fix in the message.

-->