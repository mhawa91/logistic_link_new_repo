<?php

namespace Modules\ModuleManager\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class AssistantCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Interface command to make actions more responsive.';

    protected $actions = [
        'Create model'           => 'createModel',
        'Create model service'   => 'createModelService',
        'Create crud controller' => 'createCrudController',
        'Create crud views'      => 'createCrudViews',
    ];

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
        $ACTION = $this->choice('What do you want to do?', array_keys($this->actions), 0);
        $this->{ $this->actions[ $ACTION ] }();
    }

    protected function createModel()
    {
        $ModelName   = $this->ask('Model name?');
        $Translated  = $this->anticipate('Is translated?' , ['y', 'n'], 'y') == 'y' ? true : false;
        $SoftDeleted = $this->anticipate('Is soft dleted?', ['y', 'n'], 'y') == 'y' ? true : false;
        $ModuleName  = $this->ask('Module name?');
        $Parameters  = [
            'model'  => $ModelName,
            'module' => $ModuleName,
        ];
        if ($Translated) {
            $Parameters['-t'] = true;
        }
        if ($SoftDeleted) {
            $Parameters['-s'] = true;
        }
        $this->call('system:make:model', $Parameters);
    }

    protected function createModelService()
    {
        $ServiceName = $this->ask('Service name?');
        $ModelName   = $this->ask('Model name?');
        $ModuleName  = $this->ask('Module name?');
        $Parameters  = [
            'service' => $ServiceName,
            'model'   => $ModelName,
            'module'  => $ModuleName,
        ];
        $this->call('system:make:model-service', $Parameters);
    }

    protected function createCrudController()
    {
        $ControllerName = $this->ask('Controller name?');
        $ServiceName    = $this->ask('Service name?');
        $ModuleName     = $this->ask('Module name?');
        $Parameters     = [
            'controller' => $ControllerName,
            'service'    => $ServiceName,
            'module'     => $ModuleName,
        ];
        $this->call('system:make:http-crud-controller', $Parameters);
    }

    protected function createCrudViews()
    {
        $FolderName     = $this->ask('Folder name?');
        $ControllerName = $this->ask('Controller name?');
        $ModuleName     = $this->ask('Module name?');
        $Parameters     = [
            'folder'     => $FolderName,
            'controller' => $ControllerName,
            'module'     => $ModuleName,
        ];
        $this->call('system:make:crud-views', $Parameters);
    }
}
