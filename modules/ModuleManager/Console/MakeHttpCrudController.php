<?php

namespace Modules\ModuleManager\Console;

use Illuminate\Support\Str;
use Nwidart\Modules\Commands\GeneratorCommand;
use Nwidart\Modules\Support\Config\GenerateConfigReader;
use Nwidart\Modules\Support\Stub;
use Nwidart\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeHttpCrudController extends GeneratorCommand
{
    use ModuleCommandTrait;

    /**
     * The name of argument being used.
     *
     * @var string
     */
    protected $argumentName = 'controller';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'system:make:http-crud-controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new http controller Powered By ModuleManager for the specified module.';

    public function handle() : int
    {
        $Result = parent::handle();
        if ($Result === E_ERROR) {
            return E_ERROR;
        }
        $this->afterHandle();
        return $Result;
    }

    public function afterHandle()
    {
        $module     = $this->laravel['modules']->findOrFail($this->getModuleName());
        $Controller = $this->getControllerName();
        $Folder     = $this->getFolderName();
        $RoutesToBeAdded = "
            Route::prefix('". $Folder ."')->group(function() {
                Route::get('list'           , '". $Controller ."@index')->name('". $Controller ."@index');
                Route::post('list'          , '". $Controller ."@postIndex')->name('". $Controller ."@postIndex');
                Route::get('create'         , '". $Controller ."@create')->name('". $Controller ."@create');
                Route::post('create'        , '". $Controller ."@postCreate')->name('". $Controller ."@postCreate');
                Route::get('update/{model}' , '". $Controller ."@update')->name('". $Controller ."@update');
                Route::post('update'        , '". $Controller ."@postUpdate')->name('". $Controller ."@postUpdate');
                Route::post('delete'        , '". $Controller ."@postDelete')->name('". $Controller ."@postDelete');
                Route::post('restore'       , '". $Controller ."@postRestore')->name('". $Controller ."@postRestore');
                Route::post('perma-delete'  , '". $Controller ."@postPermaDelete')->name('". $Controller ."@postPermaDelete');
                Route::post('status-update' , '". $Controller ."@postStatus')->name('". $Controller ."@postStatus');
                Route::get('select'         , '". $Controller ."@select')->name('". $Controller ."@select');
                Route::post('export'        , '". $Controller ."@postExport')->name('". $Controller ."@postExport');
                Route::get('{model}'        , '". $Controller ."@read')->name('". $Controller ."@read');
                Route::post('{model}'       , '". $Controller ."@postRead')->name('". $Controller ."@postRead');
            });
        ";
        $TranslationsToBeAdded = "
            '". $Folder ."' => [
                'header' => '". $Folder ."',
                'menu'   => '". $Folder ."',
                'list'   => 'List ". $Folder ."',
                'create' => 'Create new ". $Folder ."',
                'update' => 'Update ". $Folder ."',
            ],
        ";
        $MenuToBeAdded = "
            app('Ui')->registerMainMenuItem([
                'id'        => '". $Controller ."@all',
                'parent_id' => '". $Controller ."',
                'ordering'  => 0,
                'type'      => 'MENU', //MENU | HEADER
                'link'      => route('". $Controller ."@index'),
                'text'      => __('". $module->getLowerName() ."::strings.". $Folder .".menu'),
                'icon'      => app('Ui')->defaultIcon(),
                'show'      => function() { return true; },
            ]);
            app('Ui')->registerMainMenuItem([
                'id'        => '". $Controller ."@index',
                'parent_id' => '". $Controller ."@all',
                'ordering'  => 0,
                'type'      => 'MENU', //MENU | HEADER
                'link'      => route('". $Controller ."@index'),
                'text'      => __('admin::strings.global.list_all'),
                'icon'      => app('Ui')->defaultIcon(),
                'show'      => function() { return true; },
            ]);
            app('Ui')->registerMainMenuItem([
                'id'        => '". $Controller ."@create',
                'parent_id' => '". $Controller ."@all',
                'ordering'  => 0,
                'type'      => 'MENU', //MENU | HEADER
                'link'      => route('". $Controller ."@create'),
                'text'      => __('admin::strings.global.add_new'),
                'icon'      => app('Ui')->defaultIcon(),
                'show'      => function() { return true; },
            ]);
        ";
        $this->info("Please Add These Lines inside web.php File:");
        $this->info($RoutesToBeAdded);
        $this->info("Please Add These Lines inside modules/". $module->getStudlyName() ."/Resources/lang/{locale}/strings.php File:");
        $this->info($TranslationsToBeAdded);
        $this->info("Please Add These Lines inside ModuleServiceProvider File:");
        $this->info($MenuToBeAdded);
    }

    /**
     * Get controller name.
     *
     * @return string
     */
    public function getDestinationFilePath()
    {
        $path = $this->laravel['modules']->getModulePath($this->getModuleName());

        $controllerPath = GenerateConfigReader::read('controller');

        return $path . $controllerPath->getPath() . '/' . $this->getControllerName() . '.php';
    }

    /**
     * @return string
     */
    protected function getTemplateContents()
    {
        $module = $this->laravel['modules']->findOrFail($this->getModuleName());

        return (new Stub($this->getStubName(), [
            'MODULENAME'        => $module->getStudlyName(),
            'CONTROLLERNAME'    => $this->getControllerName(),
            'NAMESPACE'         => $module->getStudlyName(),
            'CLASS_NAMESPACE'   => $this->getClassNamespace($module),
            'CLASS'             => $this->getControllerNameWithoutNamespace(),
            'LOWER_NAME'        => $module->getLowerName(),
            'MODULE'            => $this->getModuleName(),
            'NAME'              => $this->getModuleName(),
            'STUDLY_NAME'       => $module->getStudlyName(),
            'MODULE_NAMESPACE'  => $this->laravel['modules']->config('namespace'),
            'SERVICE_CLASS'     => $this->getServiceClassName(),
            'FOLDER_NAME'       => $this->getFolderName(),
        ]))->render();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['controller', InputArgument::REQUIRED, 'The name of the controller class.'],
            ['service', InputArgument::REQUIRED, 'The class name of the model service that will be controllerd.'],
            ['module', InputArgument::REQUIRED, 'The name of module will be used.'],
        ];
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }

    /**
     * @return array|string
     */
    protected function getFolderName()
    {
        return Str::snake(
            str_replace(['controller'], '', strtolower($this->getControllerName()))
        );
    }

    /**
     * @return array|string
     */
    protected function getServiceClassName()
    {
        $service = Str::studly($this->argument('service'));

        if (Str::contains(strtolower($service), 'service') === false) {
            $service .= 'Service';
        }

        return $service;
    }

    /**
     * @return array|string
     */
    protected function getControllerName()
    {
        $controller = Str::studly($this->argument('controller'));

        if (Str::contains(strtolower($controller), 'controller') === false) {
            $controller .= 'Controller';
        }

        return str_replace('Controller', 'Controller', $controller);
    }

    /**
     * @return array|string
     */
    private function getControllerNameWithoutNamespace()
    {
        return class_basename($this->getControllerName());
    }

    public function getDefaultNamespace() : string
    {
        $module = $this->laravel['modules'];

        $path = $module->config('paths.generator.controller.namespace') ?: $module->config('paths.generator.controller.path', 'Http/Controllers');

        return $path;
    }

    /**
     * Get the stub file name based on the options
     * @return string
     */
    protected function getStubName()
    {
        return '/controller-http-crud.stub';
    }
}
