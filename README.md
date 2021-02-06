# laravel-watcher
Set a watcher to watch for specific keys in your Laravel form requests. If those keys are present, perform any arbitrary functions(s).

* [Installation](#installation)
* [Usage](#usage)
  * [Set new setting](#set-new-setting)
  * [Get all settings](#get-all-settings)
  * [Get single setting](#get-single-setting)
  * [Get certain setting (via array)](#get-certain-settings)
  * [Check if a setting is set](#check-if-a-setting-is-set)
* [Encryption](#encryption)
* [Multi-tenancy](#multi-tenancy)
* [Disable cache](#disable-cache)
* [Hidden settings](#hidden-settings)
* [Customize table name](#customize-table-name)
  
  
## Installation
This package can be used in Laravel 5.4 or higher.

You can install the package via composer:

``` bash
composer require hackeresq/laravel-watcher
```

Watcher is a trait that can be added to your Laravel FormRequests (or you can use the provided base FormRequest). To start using the `setWatcher()` method, you must 'use' the Watcher trait by either adding it to your FormRequest or using the base WatcherRequest in your controllers.

An example of a custom FormRequest implementation:

```php
<?php

namespace App\Http\Requests;

use hackerESQ\Watcher\Watcher;
use Illuminate\Foundation\Http\FormRequest;

class SettingsRequest extends FormRequest
{
    use Watcher;
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // some rules
            'name' => ['sometimes', 'required', 'string'],
        ];
    }
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
        //...
    }
```

<b>Success!</b> laravel-watcher is now installed!

## Usage

Once you've added the trait to your custom FormRequest or you've added the WatcherRequest to your controller method, you'll have a new `setWatcher()` method available on your requests. This allows you to set up your watcher.

The basic usage of the `setWatcher()` method is to pass an array, with the "trigger" as the key, like so:

```php
        $request->setWatcher([
            'invoice_start_num_changed' => [
                'action' => fn($context) => DB::statement("ALTER TABLE `invoices` AUTO_INCREMENT = ".(int)$context->request->invoice_start_num),
            ],
        ]);
```

You will notice that the key is the "watched" trigger. Then you define an 'action' which is an anonymous function. 

You can optionally choose to remove the trigger from the request (e.g. if you are passing the request elsewhere and want to sanitize it) by passing a 'removeKey' attribute, like this:

```php
        $request->setWatcher([
            'invoice_start_num_changed' => [
                'action' => fn($context) => DB::statement("ALTER TABLE `invoices` AUTO_INCREMENT = ".(int)$context->request->invoice_start_num),
                'removeKey' => true,
            ],
        ]);
```

## Finally

### Contributing
Feel free to create a fork and submit a pull request if you would like to contribute.

### Bug reports
Raise an issue on GitHub if you notice something broken.

