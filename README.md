# Captain Hook

PHP library to provide a generic hooks utility that can run one off scripts.

## Installation instructions

> This is not found on packagist...


### Composer 

Add this to the `repositories` section to your projects `composer.json`:

```
{
	"type": "vcs",
	"url": "https://github.com/adamkopenplay/cptnhook"
}
```

Add this to the `require` section of your composer.json:

```
"adamkopenplay/cptnhook": "X.X.X"
```

> Use whichever version is most suitable.


Run the following command:

```
$ composer update adamkopenplay/cptnhook
```

### Laravel

Register the Service provider in `config/app.php`:

```
'providers' => [
	...
	CptnHook\Integration\Laravel\CptnHookServiceProvider::class,
	...
]
```

Publish the configuration file to your application:

```
$ php artisan vendor:publish --provider="CptnHook\Integration\Laravel\CptnHookServiceProvider"
```

Now update the configuration as required.
