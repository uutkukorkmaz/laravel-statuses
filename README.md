[![Laravel Statuses](https://github.com/uutkukorkmaz/laravel-statuses/tree/main/.github/package-banner.png)]
# Laravel Statuses

[![Latest Version on Packagist](https://img.shields.io/packagist/v/uutkukorkmaz/laravel-statuses.svg?style=flat-square)](https://packagist.org/packages/uutkukorkmaz/laravel-statuses)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/uutkukorkmaz/laravel-statuses/run-tests?label=tests)](https://github.com/uutkukorkmaz/laravel-statuses/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/uutkukorkmaz/laravel-statuses/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/uutkukorkmaz/laravel-statuses/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/uutkukorkmaz/laravel-statuses.svg?style=flat-square)](https://packagist.org/packages/uutkukorkmaz/laravel-statuses)

Laravel Statuses is a package that makes managing the model statuses easier. It provides a trait that you can use in
your models to add statuses to them.

## Installation

You can install the package via composer:

```bash
composer require uutkukorkmaz/laravel-statuses
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-statuses-config"
```

This is the contents of the published config file:

```php
return [

    'namespace' => 'Enums\\Statuses',

    'allow_sequential' => true,

];
```

## Usage

### Basic Usage

To create a new status, you can use the `status:generate` command. This will generate an `Enum` in your
project's `app/Enums` directory.

```bash
php artisan status:generate OrderStatus
```

### Defining Cases

You can define cases for your status by adding constants to your `Enum` class. The name of the constant will be used as
the case name, and the value will be used as the case value.

```bash
php artisan status:generate OrderStatus --cases Pending,Approved,Processing,Shipped,Delivered
```

The result of the above command will be:

```php
<?php

namespace App\Enums\Statuses;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
}
```

### Sequential Statuses

This type of statuses are used when the next status is always the next case in the enum. For example, if you have a
status for a user's account, the next status will always be the next case in the enum. For example, if the current
status is `PENDING`, the next status will be `APPROVED`.

```bash
php artisan status:generate AccountStatus --sequential --cases Pending,Approved
```

The result of the above command will be:

```php
<?php

namespace App\Enums\Statuses;

enum AccountStatus: string
{

    case PENDING = 'pending';
    case APPROVED = 'approved';


    public function next(): self
    {
        return match ($this) {
            self::PENDING => self::APPROVED,
            default => throw new \LogicException('Invalid status'),
        };
    }

    public function previous(): self
    {
        return match($this) {
            self::APPROVED => self::PENDING,
            default => throw new \LogicException('Invalid status'),
        };
    }
}
```

### Using Statuses with Models

To attach a status to a model, you can use the `HasStatus` trait. This trait will add a `status` field to your model.

```php
use Uutkukorkmaz\LaravelStatuses\Concerns\HasStatus;

class Order extends Model
{
    use HasStatus;
    
    // ...
}
```

You can also attach status to a model automatically when creating the status with following command:

```bash
php artisan status:generate OrderStatus --model=Order --sequential --cases Pending,Approved,Processing,Shipped,Delivered
```

Please note that before the calling the command above you should have the model and the model must
have `protected $casts` line.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// ...

class Order extends Model
{
    use HasStatus;
    
    // ...
    
    protected $casts = [
      'status' => \App\Enums\Statuses\OrderStatus::class,
    ];
    
    // ...
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [uutkukorkmaz](https://github.com/uutkukorkmaz)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
