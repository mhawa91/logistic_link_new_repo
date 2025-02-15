<?php

namespace Modules\ModuleManager\Console;

use Illuminate\Support\Str;
use Nwidart\Modules\Commands\GeneratorCommand;
use Nwidart\Modules\Support\Config\GenerateConfigReader;
use Nwidart\Modules\Support\Stub;
use Nwidart\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeCrudModel extends GeneratorCommand
{
    use ModuleCommandTrait;

    /**
     * The name of argument name.
     *
     * @var string
     */
    protected $argumentName = 'model';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'system:make:model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new model for the specified module.';

    public function handle() : int
    {
        if (parent::handle() === E_ERROR) {
            return E_ERROR;
        }

        $this->handleOptionalTranslationOption();
        $this->handleOptionalMigrationOption();

        return 0;
    }

    /**
     * Create a proper migration name:
     * ProductDetail: product_details
     * Product: products
     * @return string
     */
    private function createMigrationName()
    {
        $pieces = preg_split('/(?=[A-Z])/', $this->argument('model'), -1, PREG_SPLIT_NO_EMPTY);

        $string = '';
        foreach ($pieces as $i => $piece) {
            if ($i+1 < count($pieces)) {
                $string .= strtolower($piece) . '_';
            } else {
                $string .= Str::plural(strtolower($piece));
            }
        }

        return $string;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['model', InputArgument::REQUIRED, 'The name of model will be created.'],
            ['module', InputArgument::OPTIONAL, 'The name of module will be used.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['translated', 't', InputOption::VALUE_NONE, 'Creates translations model and addeds required traits.', null],
            ['soft', 's', InputOption::VALUE_NONE, 'Creates addeds softDelets trait.', null],
            ['fillable', 'f', InputOption::VALUE_OPTIONAL, 'The fillable attributes.', null],
            ['migration', 'm', InputOption::VALUE_NONE, 'Flag to create associated migrations', null],
        ];
    }

    private function handleOptionalTranslationOption()
    {
        if ($this->option('translated')) {
            $this->call('system:make:model', [
                'model'      => $this->getModelName() . 'Translation',
                'module'     => $this->getModuleName(),
            ]);
        }
    }

    /**
     * Create the migration file with the given model if migration flag was used
     */
    private function handleOptionalMigrationOption()
    {
        if ($this->option('migration') === true) {
            $migrationName = 'create_' . $this->createMigrationName() . '_table';
            $this->call('module:make-migration', ['name' => $migrationName, 'module' => $this->argument('module')]);
        }
    }

    /**
     * @return mixed
     */
    protected function getTemplateContents()
    {
        $module = $this->laravel['modules']->findOrFail($this->getModuleName());

        return (new Stub('/model.stub', [
            'NAME'              => $this->getModelName(),
            'FILLABLE'          => $this->getFillable(),
            'NAMESPACE'         => $this->getClassNamespace($module),
            'CLASS'             => $this->getModelName(),
            'LOWER_NAME'        => $module->getLowerName(),
            'MODULE'            => $this->getModuleName(),
            'STUDLY_NAME'       => $module->getStudlyName(),
            'MODULE_NAMESPACE'  => $this->laravel['modules']->config('namespace'),
            'IMPLEMENTAIONS'    => $this->getModelImplementations(),
            'TRAITS'               => $this->getModelTraits(),
            'TRANSLATION_PROPERTY' => $this->getModelTranslationAttribute(),
            'TRANSLATION_FILLABLE' => $this->getModelTranslationFillableAttribute(),
        ]))->render();
    }

    /**
     * @return mixed
     */
    protected function getDestinationFilePath()
    {
        $path = $this->laravel['modules']->getModulePath($this->getModuleName());

        $modelPath = GenerateConfigReader::read('model');

        return $path . $modelPath->getPath() . '/' . $this->getModelName() . '.php';
    }

    /**
     * @return mixed|string
     */
    private function getModelName()
    {
        return Str::studly($this->argument('model'));
    }

    /**
     * @return mixed|string
     */
    private function getModelTranslationAttribute()
    {
        return $this->option('translated') 
        ? 'public $translationModel = "Modules\\'. $this->getModuleName() .'\\Entities\\' . $this->getModelName() . 'Translation";'
        : '';
    }

    /**
     * @return mixed|string
     */
    private function getModelTranslationFillableAttribute()
    {
        return $this->option('translated') 
        ? 'public $translatedAttributes = [];'
        : '';
    }

    /**
     * @return mixed|string
     */
    private function getModelTraits()
    {
        $Traits = [];
        if ($this->option('soft')) {
            $Traits[] = 'SoftDeletes';
        }
        if ($this->option('translated')) {
            $Traits[] = 'Translatable';
            $Traits[] = 'TranslatableHelper';
        }
        if (empty($Traits)) {
            return '';
        }
        return 'use ' . implode(', ', $Traits) . ';';
    }

    /**
     * @return mixed|string
     */
    private function getModelImplementations()
    {
        $Implementations = [];
        if ($this->option('translated')) {
            $Implementations[] = 'TranslatableContract';
        }
        if (empty($Implementations)) {
            return '';
        }
        return 'implements ' . implode(', ', $Implementations);
    }

    /**
     * @return string
     */
    private function getFillable()
    {
        $fillable = $this->option('fillable');

        if (!is_null($fillable)) {
            $arrays = explode(',', $fillable);

            return json_encode($arrays);
        }

        return '[]';
    }

    /**
     * Get default namespace.
     *
     * @return string
     */
    public function getDefaultNamespace() : string
    {
        $module = $this->laravel['modules'];

        return $module->config('paths.generator.model.namespace') ?: $module->config('paths.generator.model.path', 'Entities');
    }
}
