Transistor API
=============

Super-simple, minimum abstraction Transistor API wrapper, in PHP.

Get from the Transistor API docs to the code as directly as possible. Or optionally use the v1 verbose command wrapper as well.

Requires PHP 7.4+.

Installation
------------

You will be able to install transistor-api using Composer, once it's ready.

<!--

DRAFT

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
-->
Examples
--------

Start by `use`-ing the class and creating an instance with your API key

```php
use \CreateWithRani\Transistor\Transistor;

$Transistor = new Transistor('lD8123432345434543');
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

This is a pretty simple wrapper with an optionally more verbose commands class if you want/need something easier than going straight to the API.

Feel free to suggest an improvement by creating an issue.

Pull requests for bugs are more than welcome - please explain the bug you're trying to fix in the message.
