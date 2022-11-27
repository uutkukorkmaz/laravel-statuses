<?php

namespace Uutkukorkmaz\LaravelStatuses\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class StatusGenerateCommand extends GeneratorCommand
{
    protected $name = 'status:generate';

    protected $description = 'Create a new status enum';

    protected $type = 'Status';

    /**
     * @var array<string>|array<string,string>
     */
    protected array $cases = ['pending', 'active'];

    protected function appendHasStatusConcernIntoModel()
    {
        if (! $this->option('model')) {
            return;
        }

        $targetModel = $this->option('model');
        $targetModelPath = app_path("Models/{$targetModel}.php");

        if (! file_exists($targetModelPath)) {
            $this->error("Model App/Models/{$targetModel} does not exist");

            return;
        }

        $casts = $this->getDefaultNamespace(rtrim(app()->getNamespace(), '\\')).'\\'.$this->argument('name');
        $replaceMap = [
            "use Illuminate\Database\Eloquent\Model;\n" => "use Illuminate\Database\Eloquent\Model;\nuse Uutkukorkmaz\LaravelStatuses\Concerns\HasStatus;\n",
            "use Illuminate\Foundation\Auth\User as Authenticatable;\n" => "use Illuminate\Foundation\Auth\User as Authenticatable;\nuse Uutkukorkmaz\LaravelStatuses\Concerns\HasStatus;\n",
            "extends Model\n{\n" => "extends Model\n{\n\tuse HasStatus;\n",
            "extends Authenticatable\n{\n" => "extends Authenticatable\n{\n\tuse HasStatus;\n",
            'protected $casts = [' => "protected \$casts = [\n\t\t'status' => \\{$casts}::class,",
        ];

        $targetModelContents = file_get_contents($targetModelPath);
        foreach ($replaceMap as $search => $replace) {
            if (! Str::contains($targetModelContents, $replace)) {
                $targetModelContents = str_replace($search, $replace, $targetModelContents);
            }
        }

        file_put_contents($targetModelPath, $targetModelContents);
    }

    protected function replaceCases($stub)
    {
        $cases = $this->getCases();

        $content = '';
        $forwards = '';
        $backwards = '';
        $caseNames = array_keys($cases);
        $sequence = ['forwards' => $caseNames, 'backwards' => array_reverse($caseNames)];

        foreach ($cases as $case => $value) {
            $content .= "\tcase {$case} = '{$value}';\n";
        }

        foreach ($sequence as $direction => $cases) {
            foreach ($cases as $case => $value) {
                if ($cases[$case + 1] ?? false) {
                    $$direction .= "self::{$value} => self::{$cases[$case + 1]},\n\t\t";
                }
            }
        }

        $replace = [
            '{{ cases }}' => ltrim($content, "\t"),
            '{{ sequence_forwards }}' => rtrim($forwards, "\n\t"),
            '{{ sequence_backwards }}' => rtrim($backwards, "\n\t"),
        ];

        return str_replace(array_keys($replace), array_values($replace), $stub);
    }

    protected function buildClass($name)
    {
        $this->appendHasStatusConcernIntoModel();

        return $this->replaceCases(parent::buildClass($name));
    }

    protected function getStub(): string
    {
        if ($this->option('sequential')) {
            return __DIR__.'/../../stubs/status.sequential.stub';
        }

        return __DIR__.'/../../stubs/status.stub';
    }

    protected function getCases()
    {
        if ($this->option('cases')) {
            $this->cases = explode(',', $this->option('cases'));
        }

        foreach ($this->cases as $key => $case) {
            $this->cases[Str::upper($case)] = Str::snake($case);
            unset($this->cases[$key]);
        }

        return $this->cases;
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\\'.config('statuses.namespace', 'Enums');
    }

    protected function getOptions()
    {
        return [
            [
                'model',
                'm',
                InputOption::VALUE_OPTIONAL,
                'The model that the status belongs to.',
            ],
            [
                'sequential',
                's',
                InputOption::VALUE_NONE,
                'Create a sequential status.',
            ],
            [
                'cases',
                'c',
                InputOption::VALUE_OPTIONAL,
                'The cases that the status enum should have.',
            ],
        ];
    }
}
