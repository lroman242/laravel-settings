# Laravel 5 Settings

### Installation

Add to your composer.json

    "require": {
       ...
      "lroman242/laravel-settings": "1.*",
       ...
    },

and

    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/lroman242/laravel-settings.git"
        }
    ]

Add provider to app.php config file.

    'providers' => [
        ...
        lroman242\LaravelSettings\SettingsServiceProvider::class,
        ...
    ],
    'aliases' => [
        ...
        'Settings' => lroman242\LaravelSettings\Facades\Settings::class,
        ...
    ],

Publish resources
    
    php artisan vendor:publish --tag=laravel-settings

Manage settings on settings.php config file

Run migrations

    php artisan migrate

### Usage
#### Has
- $name - required / string
- $module - optional / string / default = "global"
+ return bool

```
    Settings::has($name);
    Settings::has($name, $module);
``` 
#### Set
- $name - required / string
- $value - required / mixed
- $module - optional / string / default = "global"
- $active - optional / boolean / TRUE

```
    Settings::set($name, $value);
    Settings::set($name, $value, $module);
    Settings::set($name', $value, $module, $active);
```
#### Get
- $name - required / string
- $module - optional / string / default = "global"
- $default - optional / mixed / default = NULL
+ return $value / mixed / default = NULL

```
    Settings::get($name);
    Settings::get($name, $module);
    Settings::get($name, $module, $default);
```
#### Update
- $name - required / string
- $value - required / mixed
- $module - optional / string / default = "global"
- $active - optional / boolean / TRUE

```
    Settings::update($name, $value);
    Settings::update($name, $value, $module);
    Settings::update($name', $value, $module, $active);
``` 
#### Is Active
- $name - required / string
- $module - optional / string / default = "global"
+ return bool

```
    Settings::isActive($name);
    Settings::isActive($name, $module);
``` 
#### Activate
- $name - required / string
- $module - optional / string / default = "global"
+ return bool

```
    Settings::activate($name);
    Settings::activate($name, $module);
``` 
#### Deactivate
- $name - required / string
- $module - optional / string / default = "global"
+ return bool

```
    Settings::deactivate($name);
    Settings::deactivate($name, $module);
``` 
#### Delete
- $name - required / string
- $module - optional / string / default = "global"
+ return bool

```
    Settings::delete($name);
    Settings::delete($name, $module);
```

License
----

MIT
