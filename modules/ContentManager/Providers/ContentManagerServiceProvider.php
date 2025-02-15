<?php

namespace Modules\ContentManager\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;
use Modules\ContentManager\Entities;

class ContentManagerServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'ContentManager';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'contentmanager';

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

        Relation::morphMap([
            'content' => Entities\Content::class,
        ]);
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
        $this->app->booted(function() {
            app(\Modules\ContentManager\Entities\Contracts\ContentInterface::class)->register([
                'ordering'  => 0,
                'base_type' => 'POST',
                'type'      => 'article',
                'menu'      => __('contentmanager::strings.content.menu'),
                'list'      => __('contentmanager::strings.content.list'),
                'create'    => __('contentmanager::strings.content.create'),
                'update'    => __('contentmanager::strings.content.update'),
                'show'      => function() { return true; },
                'inputs'    => [
                    'category' => true
                ],
            ]);

            // dd(app(\Modules\ContentManager\Entities\Contracts\ContentInterface::class)->getTypes());
            app('Ui')->registerMainMenuItem([
                'id'        => 'ContentManager',
                'ordering'  => 20,
                'type'      => 'HEADER', //MENU | HEADER
                'text'      => __('contentmanager::strings.content_management'),
                'show'      => function() { return true; },
            ]);

            // app('Ui')->registerMainMenuItem([
            //     'id'        => 'PageManager@all',
            //     'parent_id' => 'ContentManager',
            //     'ordering'  => 1,
            //     'type'      => 'MENU', //MENU | HEADER
            //     'link'      => route('PageController@index', ['type' => 'article']),
            //     'text'      => __('contentmanager::strings.page.menu'),
            //     'icon'      => app('Ui')->defaultIcon(),
            //     'show'      => function() { return true; },
            // ]);
            // app('Ui')->registerMainMenuItem([
            //     'id'        => 'PageManager@index',
            //     'parent_id' => 'PageManager@all',
            //     'ordering'  => 0,
            //     'type'      => 'MENU', //MENU | HEADER
            //     'link'      => route('PageController@index', ['type' => 'article']),
            //     'text'      => __('admin::strings.global.list_all'),
            //     'icon'      => app('Ui')->defaultIcon(),
            //     'show'      => function() { return true; },
            // ]);
            // app('Ui')->registerMainMenuItem([
            //     'id'        => 'PageManager@create',
            //     'parent_id' => 'PageManager@all',
            //     'ordering'  => 0,
            //     'type'      => 'MENU', //MENU | HEADER
            //     'link'      => route('PageController@create', ['type' => 'article']),
            //     'text'      => __('admin::strings.global.add_new'),
            //     'icon'      => app('Ui')->defaultIcon(),
            //     'show'      => function() { return true; },
            // ]);

            // app('Ui')->registerMainMenuItem([
            //     'id'        => 'ContentManager@all',
            //     'parent_id' => 'ContentManager',
            //     'ordering'  => 2,
            //     'type'      => 'MENU', //MENU | HEADER
            //     'link'      => route('ContentController@index', ['type' => 'article']),
            //     'text'      => __('contentmanager::strings.content.menu'),
            //     'icon'      => app('Ui')->defaultIcon(),
            //     'show'      => function() { return true; },
            // ]);
            // app('Ui')->registerMainMenuItem([
            //     'id'        => 'CategoryController@index',
            //     'parent_id' => 'ContentManager@all',
            //     'ordering'  => 0,
            //     'type'      => 'MENU', //MENU | HEADER
            //     'link'      => route('CategoryController@index', ['type' => 'article']),
            //     'text'      => __('contentmanager::strings.category.menu'),
            //     'icon'      => app('Ui')->defaultIcon(),
            //     'show'      => function() { return true; },
            // ]);
            // app('Ui')->registerMainMenuItem([
            //     'id'        => 'ContentManager@index',
            //     'parent_id' => 'ContentManager@all',
            //     'ordering'  => 0,
            //     'type'      => 'MENU', //MENU | HEADER
            //     'link'      => route('ContentController@index', ['type' => 'article']),
            //     'text'      => __('admin::strings.global.list_all'),
            //     'icon'      => app('Ui')->defaultIcon(),
            //     'show'      => function() { return true; },
            // ]);
            // app('Ui')->registerMainMenuItem([
            //     'id'        => 'ContentManager@create',
            //     'parent_id' => 'ContentManager@all',
            //     'ordering'  => 0,
            //     'type'      => 'MENU', //MENU | HEADER
            //     'link'      => route('ContentController@create', ['type' => 'article']),
            //     'text'      => __('admin::strings.global.add_new'),
            //     'icon'      => app('Ui')->defaultIcon(),
            //     'show'      => function() { return true; },
            // ]);

            app('Ui')->registerMainMenuItem([
                'id'        => 'SectionController@general',
                'parent_id' => 'Settings',
                'ordering'  => 0,
                'type'      => 'MENU', //MENU | HEADER
                'link'      => route('SectionController@update'),
                'text'      => __('contentmanager::strings.sections.menu'),
                'icon'      => app('Ui')->defaultIcon(),
                'show'      => function() { return true; },
            ]);
        });
    }
}
