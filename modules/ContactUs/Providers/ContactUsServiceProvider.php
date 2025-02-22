<?php

namespace Modules\ContactUs\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class ContactUsServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'ContactUs';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'contactus';

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
        $this->app->booted(function() {

            $isRootOrAdmin = function() {
                return auth()->check() && (auth()->user()->isAn('ROOT') || auth()->user()->isAn('SYSTEM_ADMIN'));
            };

            app('Ui')->registerMainMenuItem([
                'id'        => 'ContactUs',
                'ordering'  => 8000,
                'type'      => 'HEADER', //MENU | HEADER
                'text'      => __('contactus::strings.contact_us.header'),
                'show'      => $isRootOrAdmin,
            ]);

            app('Ui')->registerMainMenuItem([
                'id'        => 'ContactUsController@all',
                'parent_id' => 'ContactUs',
                'ordering'  => 0,
                'type'      => 'MENU', //MENU | HEADER
                'link'      => route('ContactUsController@index'),
                'text'      => __('contactus::strings.contact_us.menu'),
                'icon'      => app('Ui')->defaultIcon(),
                'show'      => $isRootOrAdmin,
            ]);
        });
    }
}
