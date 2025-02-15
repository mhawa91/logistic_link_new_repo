<?php

namespace Modules\ModuleManager\Console;

use Illuminate\Support\Str;
use Nwidart\Modules\Commands\GeneratorCommand;
use Nwidart\Modules\Support\Config\GenerateConfigReader;
use Nwidart\Modules\Support\Stub;
use Nwidart\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeCrudView extends GeneratorCommand
{
    use ModuleCommandTrait;

    /**
     * The name of argument being used.
     *
     * @var string
     */
    protected $argumentName = 'folder';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'system:make:crud-view';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate full crud views (index, create, update, read) Powered By ModuleManager for the specified controller.';

    /**
     * Get destination path name.
     *
     * @return string
     */
    public function getDestinationFilePath()
    {
        $path = $this->laravel['modules']->getModulePath($this->getModuleName());

        $viewsPath = GenerateConfigReader::read('views');
        return $path . $viewsPath->getPath() . '/' . $this->getFolderName() . '/' . $this->getViewName() . '.blade.php';
    }

    /**
     * @return string
     */
    protected function getTemplateContents()
    {
        $module = $this->laravel['modules']->findOrFail($this->getModuleName());

        return (new Stub($this->getStubName(), [
            'LOWER_NAME'        => $module->getLowerName(),
            'MODULE'            => $this->getModuleName(),
            'STUDLY_NAME'       => $module->getStudlyName(),
            'CONTROLLER'        => $this->getControllerRouteName(),
            'FOLDER'            => $this->getFolderName()
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
            ['folder', InputArgument::REQUIRED, 'The name of the folder.'],
            ['view', InputArgument::REQUIRED, 'The name of the view.'],
            ['controller', InputArgument::REQUIRED, 'The name of route will be used EX: UserController will be translated to UserController@index.'],
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
     * @return string
     */
    protected function getViewName()
    {
        return strtolower($this->argument('view'));
    }

    /**
     * @return string
     */
    protected function getFolderName()
    {
        return strtolower($this->argument('folder'));
    }

    /**
     * @return string
     */
    protected function getControllerRouteName()
    {
        $controller = Str::studly($this->argument('controller'));

        if (Str::contains(strtolower($controller), 'controller') === false) {
            $controller .= 'Controller';
        }

        return str_replace('Controller', 'Controller', $controller);
    }

    /**
     * Get the stub file name based on the options
     * @return string
     */
    protected function getStubName()
    {
        return '/crud-views/'. $this->getViewName() .'.stub';
    }
}
