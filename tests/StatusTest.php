<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

function create_example_status($is_sequential = false)
{
    $path = app_path('Enums/Statuses/ExampleStatus.php');

    expect(false)->toBe(file_exists($path));

    Artisan::call(
        'status:generate',
        [
            'name' => 'ExampleStatus',
            '--sequential' => $is_sequential,
        ]
    );

    expect(true)->toBe(file_exists($path));
}

it('can create empty status enum', function () {
    create_example_status(false);

    File::cleanDirectory(app_path('Enums'));
    expect(false)->toBe(file_exists(app_path('Enums/Statuses/ExampleStatus.php')));
});

it('can create sequential status', function () {
    create_example_status(true);

    File::cleanDirectory(app_path('Enums'));
});

it('can iterate through statuses', function () {
    create_example_status(true);

    $statuses = \App\Enums\Statuses\ExampleStatus::cases();

    expect($statuses)->toBeArray();
    expect($statuses)->toHaveCount(2);
    File::cleanDirectory(app_path('Enums'));
});

it('can get status value', function () {
    create_example_status(true);

    $status = \App\Enums\Statuses\ExampleStatus::PENDING;

    expect($status->value)->toBe('pending');
    File::cleanDirectory(app_path('Enums'));
});

it('can get next status value for sequential status', function () {
    create_example_status(true);

    $status = \App\Enums\Statuses\ExampleStatus::PENDING;

    expect($status->next()->value)->toBe('active');
    File::cleanDirectory(app_path('Enums'));
});

it('can get previous status value for sequential status', function () {
    create_example_status(true);

    $status = \App\Enums\Statuses\ExampleStatus::ACTIVE;

    expect($status->previous()->value)->toBe('pending');
    File::cleanDirectory(app_path('Enums'));
});

it('should throw LogicException when trying to get next status for last status', function () {
    create_example_status(true);

    $status = \App\Enums\Statuses\ExampleStatus::ACTIVE;

    expect(fn () => $status->next())->toThrow(\LogicException::class);
    File::cleanDirectory(app_path('Enums'));
});

it('should throw LogicException when trying to get previous status for first status', function () {
    create_example_status(true);

    $status = \App\Enums\Statuses\ExampleStatus::PENDING;

    expect(fn () => $status->previous())->toThrow(\LogicException::class);
    File::cleanDirectory(app_path('Enums'));
});

it('can create model with status', function () {
    Artisan::call('make:model', ['name' => 'Example']);
    Artisan::call('status:generate', [
        'name' => 'ExampleStatus',
        '--sequential' => true,
        '--model' => 'Example',
    ]);

    $model = new \App\Models\Example();

    expect($model->getFillable())->toBeArray();
    expect('status')->toBeIn($model->getFillable());

    File::cleanDirectory(app_path('Enums'));
    File::cleanDirectory(app_path('Models'));
});
