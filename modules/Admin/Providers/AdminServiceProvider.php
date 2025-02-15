<?php

namespace Modules\Admin\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Module;
use Modules\Admin\Entities;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;
use Modules\Admin\Http\Middleware;

class AdminServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Admin';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'admin';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot(Router $router, Kernel $kernel)
    {
        // $kernel->pushMiddleware(Middleware\RestrictAccessToControlPanel::class);
        $router->pushMiddlewareToGroup('web', Middleware\RestrictAccessToControlPanel::class);
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));

        $this->registerCommands();
        $this->registerSettings();
        $this->registerVars();
        $this->registerEvents();
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
        Module::macro('installedAndEnabled', function($Module) {
            if (! Module::has($Module)) {
                return false;
            }
            if (! array_key_exists($Module, Module::allEnabled())) {
                return false;
            }
            return true;
        });
    }

    /**
     * Register global vars.
     *
     * @return void
     */
    private function registerVars(): void
    {
        $this->app->bind(Entities\Contracts\UserInterface::class, Entities\Services\UserService::class);
    }

    /**
     * Register global events.
     *
     * @return void
     */
    private function registerEvents(): void
    {
        $this->app->booted(function() {
            
            app('Ui')->registerMainMenuItem([
                'id'        => 'AdminDashboardController',
                'ordering'  => 0,
                'type'      => 'HEADER', //MENU | HEADER
                'text'      => __('admin::strings.global.dashboards'),
                'show'      => function() { return true; },
            ]);            
            app('Ui')->registerMainMenuItem([
                'id'        => 'AdminDashboardController@index',
                'parent_id' => 'AdminDashboardController',
                'ordering'  => 0,
                'type'      => 'MENU', //MENU | HEADER
                'link'      => route('AdminDashboardController@index'),
                'text'      => __('admin::strings.global.main_dashboard'),
                'icon'      => app('Ui')->defaultIcon(),
                'show'      => function() { return true; },
            ]);

            app('Ui')->registerMainMenuItem([
                'id'        => 'UsersController',
                'ordering'  => 8000,
                'type'      => 'HEADER', //MENU | HEADER
                'text'      => __('admin::strings.users.header'),
                'show'      => function() { return true; },
            ]);

            app('Ui')->registerMainMenuItem([
                'id'        => 'UsersController@all',
                'parent_id' => 'UsersController',
                'ordering'  => 0,
                'type'      => 'MENU', //MENU | HEADER
                'link'      => route('UsersController@index'),
                'text'      => __('admin::strings.users.menu'),
                'icon'      => app('Ui')->defaultIcon(),
                'show'      => function() { return true; },
            ])
            ->registerMainMenuItem([
                'id'        => 'UsersController@index',
                'parent_id' => 'UsersController@all',
                'ordering'  => 0,
                'type'      => 'MENU', //MENU | HEADER
                'link'      => route('UsersController@index'),
                'text'      => __('admin::strings.global.list_all'),
                'icon'      => app('Ui')->defaultIcon(),
                'show'      => function() { return true; },
            ])
            ->registerMainMenuItem([
                'id'        => 'UsersController@create',
                'parent_id' => 'UsersController@all',
                'ordering'  => 0,
                'type'      => 'MENU', //MENU | HEADER
                'link'      => route('UsersController@create'),
                'text'      => __('admin::strings.global.add_new'),
                'icon'      => app('Ui')->defaultIcon(),
                'show'      => function() { return true; },
            ]);


            app('Ui')->registerMainMenuItem([
                'id'        => 'Settings',
                'ordering'  => 9000,
                'type'      => 'HEADER', //MENU | HEADER
                'text'      => __('admin::strings.global.settings'),
                'show'      => function() { return true; },
            ]);
            app('Ui')->registerMainMenuItem([
                'id'        => 'ConfigController@general',
                'parent_id' => 'Settings',
                'ordering'  => 0,
                'type'      => 'MENU', //MENU | HEADER
                'link'      => route('ConfigController@update'),
                'text'      => __('admin::strings.config.menu'),
                'icon'      => app('Ui')->defaultIcon(),
                'show'      => function() { return true; },
            ]);
        });
    }
}
