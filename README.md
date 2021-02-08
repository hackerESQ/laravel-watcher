# laravel-watcher

[![Latest Version on Packagist](https://img.shields.io/packagist/v/hackerESQ/laravel-watcher.svg?style=flat-square)](https://packagist.org/packages/hackerESQ/laravel-watcher)
[![Total Downloads](https://img.shields.io/packagist/dt/hackerESQ/laravel-watcher.svg?style=flat-square)](https://packagist.org/packages/hackerESQ/laravel-watcher)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

Set a watcher to watch for specific keys in your Laravel form requests. If those keys are present, perform any arbitrary functions(s).

* [Installation](#installation)
* [Usage](#usage)
* [Complete Example](#complete-example)
  
  
## Installation
This package can be used in Laravel 5.4 or higher.

You can install the package via composer:

``` bash
composer require hackeresq/laravel-watcher
```

Watcher is a trait that can be added to your Laravel FormRequests (or you can use the included base FormRequest). To start using the `setWatcher()` method, you must 'use' the Watcher trait by either adding it to your FormRequest or using the base [WatcherRequest](https://github.com/hackerESQ/laravel-watcher/blob/master/src/Requests/WatcherRequest.php) in your controllers.

An example of a custom FormRequest implementation:

```php
<?php

namespace App\Http\Requests;

use hackerESQ\Watcher\Watcher;
use Illuminate\Foundation\Http\FormRequest;

class YourCustomFormRequest extends FormRequest
{
    use Watcher;
    
    // ...
}
```

Alternatively, if you are not using custom FormRequests, you can use the provided WatcherRequest, which already has the trait added. This is how your controller methods should look:

```php
    /**
     * Update the specified resource in storage.
     *
     * @param  hackerESQ\Watcher\Requests\WatcherRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(WatcherRequest $request)
    {   
        // ...
    }
```

<b>Success!</b> laravel-watcher is now installed!

## Usage

Once you've added the trait to your custom FormRequest or you've added the WatcherRequest to your controller method, you'll have a new `setWatcher()` method available on your requests. This allows you to set up your watcher.

### Basic Usage

The basic usage of the `setWatcher()` method is to pass an array, with the "trigger" as the key, like so:

```php
        $request->setWatcher([
            'invoice_start_num_changed' => [
                'action' => fn($context) => DB::statement("ALTER TABLE `invoices` AUTO_INCREMENT = ".(int)$context->request->invoice_start_num),
            ],
        ]);
```

You will notice that the key is the "watched" trigger. If the 'invoice_start_num_changed' is present (and not falsey) the defined 'action,' which is an anonymous function, will be called. 

### Remove Key

You can optionally choose to remove the trigger from the request (e.g. if you are passing the request elsewhere and want to sanitize it) by passing a 'removeKey' attribute, like this:

```php
        $request->setWatcher([
            'invoice_start_num_changed' => [
                'action' => fn($context) => DB::statement("ALTER TABLE `invoices` AUTO_INCREMENT = ".(int)$context->request->invoice_start_num),
                'removeKey' => true,
            ],
        ]);
```

### Passing Context

Finally, you will notice you can pass `$context` to the anonymous function. This `$context` object contains the trigger name (in the `$context->trigger` object) and the original form request (in the `$context->request` object). 

### Call multiple functions

If you do not want to create and call an intermediary function, and would prefer to call multiple functions from within the watcher, you can opt to use a standard anonymous function (rather than the short arrow function), like this:

```php
        $request->setWatcher([
            'invoice_start_num_changed' => [
                'action' => function($context) { 
                      Log::info($context); 
                      DB::statement("ALTER TABLE `invoices` AUTO_INCREMENT = ".(int)$context->request->invoice_start_num);
                      // do other stuff
                 },
                'removeKey' => true,
            ],
        ]);
```

## Complete Example

### SettingsController.php

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use hackerESQ\Watcher\Requests\WatcherRequest;

class SettingsController extends Controller
{
    // ... other controller methods
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \hackerESQ\Watcher\Requests\WatcherRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(WatcherRequest $request)
    {   
        $request->setWatcher([
            'invoice_start_num_changed' => [
                'action' => fn($context) => DB::statement("ALTER TABLE `invoices` AUTO_INCREMENT = ".(int)$context->request->invoice_start_num),
                'removeKey' => true,
            ],
            'should_log_action' => [
                'action' => fn($context) => Log::info($context),
            ],
        ]);

        Settings::set($request->all());

        return $request;
    }
}
```

## Finally

### Contributing
Feel free to create a fork and submit a pull request if you would like to contribute.

### Bug reports
Raise an issue on GitHub if you notice something broken.

