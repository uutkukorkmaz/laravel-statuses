<?php

namespace Uutkukorkmaz\LaravelStatuses\Commands;

class StatusGenerateCommand extends \Illuminate\Console\GeneratorCommand
{

    protected $name = 'status:generate';

    protected $description = 'Create a new status enum';

    protected $type = 'Status';

    protected function getStub(): string
    {
        $isSequential = $this->option('sequential');
        $sequentialSuffix = $isSequential && config('statuses.allow_sequential') ? '.sequential' : '';
        return __DIR__."/../../stubs/status{$sequentialSuffix}.stub";
    }

    protected function getDefaultNamespace($rootNamespace): string
    {

        return $rootNamespace.'\\'.config('statuses.namespace');
    }

    protected function getOptions()
    {
        return [
            ['sequential', 's', \Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Generate sequential status values.'],
        ];
    }

}
