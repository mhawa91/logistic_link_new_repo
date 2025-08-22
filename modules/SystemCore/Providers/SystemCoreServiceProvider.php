<?php

namespace Modules\SystemCore\Providers;

use Exception;
use View;
use Bouncer;
use LaravelLocalization;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Routing\Router;
use Illuminate\Contracts\Http\Kernel;
use Modules\SystemCore\Http\Middleware;
use Modules\SystemCore\Exceptions;
use Modules\SystemCore\Console;
use Modules\SystemCore\System;
use Modules\SystemCore\Http\Responses\ActionResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\SystemCore\Entities\Contracts\SystemConfigInterface;

class SystemCoreServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'SystemCore';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'systemcore';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot(Router $router, Kernel $kernel)
    {
        $kernel->pushMiddleware(Middleware\ProcessingStackMiddleware::class);
        $router->aliasMiddleware('ApiAuth'        , Middleware\JwtMiddleware::class);
        $router->aliasMiddleware('HttpAuth'       , Middleware\AuthMiddleware::class);
        $router->aliasMiddleware('NeedPermissions', Middleware\PermissionsMiddleware::class);

        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));

        $this->registerCommands();
        $this->registerSettings();
        $this->registerVars();
        $this->registerEvents();

        JsonResource::withoutWrapping();

        foreach ($this->app->config[$this->moduleNameLower]['implemntations'] as $Contract => $Service) {
            $this->app->bind($Contract, $Service);
        }

        if (! $this->app->runningInConsole()) { 
            $SystemConfig = app(SystemConfigInterface::class);
            $SystemConfig->load();
            view()->share('SystemConfig', $SystemConfig);
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
        $this->app->singleton(System\Classes\Helpers::class);
        $this->app->singleton('System', System\Core::class);
        $this->app->bind(System\Contracts\EloquentInterface::class, System\Services\EloquentBaseService::class);
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
        
    }

    /**
     * Register settings.
     *
     * @return void
     */
    private function registerSettings(): void
    {
        Bouncer::useAbilityModel(config('systemcore.models.Ability'));
        Bouncer::useRoleModel(config('systemcore.models.Role'));

        if (! $this->app->runningInConsole()) {
            app('System')->registerExceptionHandler(function(\Illuminate\Validation\ValidationException $e, $Request) {
                $Fields = collect([]);
                foreach ($e->errors() as $Name => $Messages) {
                    $Fields->push([
                        'field'   => $Name,
                        'message' => count($Messages) > 0 ? $Messages[0] : '',
                    ]);
                }
                return app(ActionResponse::class)->module('Admin')->code('422')->with([
                    'invalid_fields' => $Fields->toArray()
                ])
                ->fail();
            });
        }

        Blueprint::macro('systemStatus', function ($length = 20) {
            return $this->string('sys_status', $length)->nullable();
        });
        Blueprint::macro('systemLog', function () {
            $this->integer('created_by_user_id')->unsigned()->nullable();
            $this->integer('updated_by_user_id')->unsigned()->nullable();
            // $this->foreign('created_by_user_id')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            // $this->foreign('updated_by_user_id')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
        });
        Blueprint::macro('systemCrud', function () {
            $this->systemStatus();
            $this->systemLog();
            $this->softDeletes();
            $this->timestamps();
        });

        $this->app->singleton('GraphManager', function($app){
            return app()->make('S3GraphManager');
            require_once(
                public_path('graph/GraphManager.php')
            );
            return new \GraphManager(
                asset('/')
            );
        });
        $this->app->singleton('S3GraphManager', function($app){
            require_once(
                public_path('graph/S3GraphManager.php')
            );
            return new \S3GraphManager(
                'cdn', config('filesystems.disks.cdn')['url'], config('filesystems.disks.cdn')
            );
        });

        $this->app->booted(function() {
            $DISKS          = config('filesystems.disks');
            $DISKS['graph'] = [
                'driver'     => 'local',
                'root'       => public_path('graph/uploads/original'),
                'url'        => asset('uploads'),
                'visibility' => 'public',
            ];
            config([
                'filesystems.disks' => $DISKS
            ]);
        });
    }

    /**
     * Register global vars.
     *
     * @return void
     */
    private function registerVars(): void
    {
        View::share('_DIR_', LaravelLocalization::getCurrentLocaleDirection());
        View::share('_LOCALE_', LaravelLocalization::getCurrentLocale());
        $_ALL_LOCALES_         = LaravelLocalization::getLocalesOrder();
        $_ALL_DISPLAY_LOCALES_ = LaravelLocalization::getLocalesOrder();
        $this->app->booted(function() use($_ALL_LOCALES_, $_ALL_DISPLAY_LOCALES_) {
            foreach ($_ALL_LOCALES_ ?? [] as $key => $value) {
                if (! in_array($key, config('ui.frontend_display_language', []))) {
                    unset($_ALL_LOCALES_[$key]);
                }
            }
            View::share('_ALL_LOCALES_', $_ALL_LOCALES_);
            View::share('_ALL_DISPLAY_LOCALES_', $_ALL_DISPLAY_LOCALES_);
        });
    }

    /**
     * Register global events.
     *
     * @return void
     */
    private function registerEvents(): void
    {
        $this->app->booted(function () {

            app('Ui')->registerMainMenuItem([
                'id'        => 'CountriesController@all',
                'parent_id' => 'Settings',
                'ordering'  => 0,
                'type'      => 'MENU', //MENU | HEADER
                'link'      => route('CountriesController@index'),
                'text'      => __('systemcore::strings.countries.header'),
                'icon'      => app('Ui')->defaultIcon(),
                'show'      => function() { return true; },
            ])
            ->registerMainMenuItem([
                'id'        => 'CountriesController@index',
                'parent_id' => 'CountriesController@all',
                'ordering'  => 0,
                'type'      => 'MENU', //MENU | HEADER
                'link'      => route('CountriesController@index'),
                'text'      => __('systemcore::strings.countries.list'),
                'icon'      => app('Ui')->defaultIcon(),
                'show'      => function() { return true; },
            ])
            ->registerMainMenuItem([
                'id'        => 'CitiesController@index',
                'parent_id' => 'CountriesController@all',
                'ordering'  => 0,
                'type'      => 'MENU', //MENU | HEADER
                'link'      => route('CitiesController@index'),
                'text'      => __('systemcore::strings.cities.list'),
                'icon'      => app('Ui')->defaultIcon(),
                'show'      => function() { return true; },
            ]);
            // ->registerMainMenuItem([
            //     'id'        => 'MunicipalitiesController@index',
            //     'parent_id' => 'CountriesController@all',
            //     'ordering'  => 0,
            //     'type'      => 'MENU', //MENU | HEADER
            //     'link'      => route('MunicipalitiesController@index'),
            //     'text'      => __('systemcore::strings.municipalities.list'),
            //     'icon'      => app('Ui')->defaultIcon(),
            //     'show'      => function() { return true; },
            // ])
            // ->registerMainMenuItem([
            //     'id'        => 'NeighbourhoodsController@index',
            //     'parent_id' => 'CountriesController@all',
            //     'ordering'  => 0,
            //     'type'      => 'MENU', //MENU | HEADER
            //     'link'      => route('NeighbourhoodsController@index'),
            //     'text'      => __('systemcore::strings.neighbourhoods.list'),
            //     'icon'      => app('Ui')->defaultIcon(),
            //     'show'      => function() { return true; },
            // ]);
        });
    }
}
