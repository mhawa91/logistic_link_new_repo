<?php

namespace Modules\Address\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Facades\View;
use Modules\Address\Entities;

class AddressServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Address';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'address';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));

        $this->registerCommands();
        $this->registerSettings();
        $this->registerVars();
        $this->registerEvents();

        foreach ($this->app->config[$this->moduleNameLower]['implemntations'] as $Contract => $Service) {
            $this->app->bind($Contract, $Service);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }

    /**
     * Register commands.
     *
     * @return void
     */
    private function registerCommands(): void
    {
        // if ($this->app->runningInConsole()) {
        //     $this->commands([
        //         YourCommand::class,
        //     ]);
        // }
    }

    /**
     * Register settings.
     *
     * @return void
     */
    private function registerSettings(): void
    {
    }

    /**
     * Register global vars.
     *
     * @return void
     */
    private function registerVars(): void
    {
    }

    /**
     * Register global events.
     *
     * @return void
     */
    private function registerEvents(): void
    {
        View::composer('ui::layouts.auth', Entities\MapsViewComposer::class);
        View::composer('ui::layouts.main', Entities\MapsViewComposer::class);

        $this->app->booted(function() {
            // app('Ui')->registerMainMenuItem([
            //     'id'        => 'AdminDashboardController',
            //     'ordering'  => 0,
            //     'type'      => 'HEADER', //MENU | HEADER
            //     'text'      => 'Dashboards',
            //     'show'      => function() { return true; },
            // ]);

            // app('Ui')->registerMainMenuItem([
            //     'id'        => 'Address',
            //     'ordering'  => 8000,
            //     'type'      => 'HEADER', //MENU | HEADER
            //     'text'      => 'Menu Label',
            //     'show'      => function() { return true; },
            // ]);

            // app('Ui')->registerMainMenuItem([
            //     'id'        => 'Address@all',
            //     'parent_id' => 'Address',
            //     'ordering'  => 0,
            //     'type'      => 'MENU', //MENU | HEADER
            //     'link'      => 'javascript:;',
            //     'text'      => __('admin::strings.users.menu'),
            //     'icon'      => app('Ui')->defaultIcon(),
            //     'show'      => function() { return true; },
            // ]);
            // app('Ui')->registerMainMenuItem([
            //     'id'        => 'Address@index',
            //     'parent_id' => 'Address@all',
            //     'ordering'  => 0,
            //     'type'      => 'MENU', //MENU | HEADER
            //     'link'      => 'javascript:;',
            //     'text'      => __('admin::strings.global.list_all'),
            //     'icon'      => app('Ui')->defaultIcon(),
            //     'show'      => function() { return true; },
            // ]);
            // app('Ui')->registerMainMenuItem([
            //     'id'        => 'Address@create',
            //     'parent_id' => 'Address@all',
            //     'ordering'  => 0,
            //     'type'      => 'MENU', //MENU | HEADER
            //     'link'      => 'javascript:;',
            //     'text'      => __('admin::strings.global.add_new'),
            //     'icon'      => app('Ui')->defaultIcon(),
            //     'show'      => function() { return true; },
            // ]);
        });
    }
}
