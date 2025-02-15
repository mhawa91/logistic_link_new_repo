<?php

namespace Modules\ModuleManager\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Nwidart\Modules\Support\Config\GenerateConfigReader;
use Illuminate\Support\Str;

class MakeCrudViews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'system:make:crud-views';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate full crud views (index, create, update, read) Powered By ModuleManager for the specified controller.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $dir        = $this->getDestinationPath();
        $folder     = $this->getFolderName();
        $controller = $this->getControllerName();
        $module     = $this->getModuleName();

        if ($this->laravel['files']->isDirectory($dir)) {
            $this->error('Folder: [' . $dir . '/] allready exists');
            return 0;
        }

        $this->laravel['files']->makeDirectory($dir, 0777, true);
        $this->info('Created: ' . $dir . '/');
        $this->call('system:make:crud-view', [
            'folder'     => $folder,
            'view'       => 'index',
            'controller' => $controller,
            'module'     => $module,
        ]);
        $this->call('system:make:crud-view', [
            'folder'     => $folder,
            'view'       => 'create',
            'controller' => $controller,
            'module'     => $module,
        ]);
        $this->call('system:make:crud-view', [
            'folder'     => $folder,
            'view'       => 'update',
            'controller' => $controller,
            'module'     => $module,
        ]);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['folder', InputArgument::REQUIRED, 'The name of folder will be used.'],
            ['controller', InputArgument::REQUIRED, 'The name of route will be used EX: UserController will be translated to UserController@index.'],
            ['module', InputArgument::REQUIRED, 'The name of module will be used.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }

    /**
     * Get destination path name.
     *
     * @return string
     */
    public function getDestinationPath()
    {
        $path = $this->laravel['modules']->getModulePath($this->getModuleName());

        $viewsPath = GenerateConfigReader::read('views');

        return $path . $viewsPath->getPath() . '/' . $this->getFolderName();
    }

    /**
     * Get folder name.
     *
     * @return string
     */
    public function getFolderName()
    {
        return strtolower($this->argument('folder'));
    }

    /**
     * @return string
     */
    protected function getControllerName()
    {
        $controller = Str::studly($this->argument('controller'));

        if (Str::contains(strtolower($controller), 'controller') === false) {
            $controller .= 'Controller';
        }

        return $controller;
    }

    /**
     * @return string
     */
    protected function getModuleName(): string
    {
        $module = $this->argument('module') ?: app('modules')->getUsedNow();

        $module = app('modules')->findOrFail($module);

        return $module->getStudlyName();
    }
}
