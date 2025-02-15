<?php

namespace Modules\ContentTypes\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class ContentTypesServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'ContentTypes';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'contenttypes';

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

            app(\Modules\SystemCore\Entities\Contracts\SystemConfigInterface::class)->register([
                'ordering'  => 0,
                'key'       => 'phone_number',
                'input'     => function($valueFromDB) { 
                    return view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'text',
                            'name'        => 'config[phone_number][val]',
                            'label'       => __('contenttypes::inputs.phone_number.label'),
                            'placeholder' => __('contenttypes::inputs.phone_number.placeholder'),
                            'description' => __('contenttypes::inputs.phone_number.description'),
                            'value'       => $valueFromDB,
                        ],
                    ])->render(); 
                },
                'update'    => function($oldValues, $valueFromRequest) { 
                    app(\Modules\SystemCore\Entities\Contracts\SystemConfigInterface::class)->create([
                        'key'   => $valueFromRequest['key'],
                        'value' => $valueFromRequest['val'],
                    ]);
                    return true; 
                },
                'show'      => function() { return true; },
            ]);
            app(\Modules\SystemCore\Entities\Contracts\SystemConfigInterface::class)->register([
                'ordering'  => 0,
                'key'       => 'contact_email',
                'input'     => function($valueFromDB) { 
                    return view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'text',
                            'name'        => 'config[contact_email][val]',
                            'label'       => __('contenttypes::inputs.contact_email.label'),
                            'placeholder' => __('contenttypes::inputs.contact_email.placeholder'),
                            'description' => __('contenttypes::inputs.contact_email.description'),
                            'value'       => $valueFromDB,
                        ],
                    ])->render(); 
                },
                'update'    => function($oldValues, $valueFromRequest) { 
                    app(\Modules\SystemCore\Entities\Contracts\SystemConfigInterface::class)->create([
                        'key'   => $valueFromRequest['key'],
                        'value' => $valueFromRequest['val'],
                    ]);
                    return true; 
                },
                'show'      => function() { return true; },
            ]);
            app(\Modules\SystemCore\Entities\Contracts\SystemConfigInterface::class)->register([
                'ordering'  => 0,
                'key'       => 'facebook',
                'input'     => function($valueFromDB) { 
                    return view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'text',
                            'name'        => 'config[facebook][val]',
                            'label'       => __('contenttypes::inputs.facebook.label'),
                            'placeholder' => __('contenttypes::inputs.facebook.placeholder'),
                            'description' => __('contenttypes::inputs.facebook.description'),
                            'value'       => $valueFromDB,
                        ],
                    ])->render(); 
                },
                'update'    => function($oldValues, $valueFromRequest) { 
                    app(\Modules\SystemCore\Entities\Contracts\SystemConfigInterface::class)->create([
                        'key'   => $valueFromRequest['key'],
                        'value' => $valueFromRequest['val'],
                    ]);
                    return true; 
                },
                'show'      => function() { return true; },
            ]);
            app(\Modules\SystemCore\Entities\Contracts\SystemConfigInterface::class)->register([
                'ordering'  => 0,
                'key'       => 'twitter',
                'input'     => function($valueFromDB) { 
                    return view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'text',
                            'name'        => 'config[twitter][val]',
                            'label'       => __('contenttypes::inputs.twitter.label'),
                            'placeholder' => __('contenttypes::inputs.twitter.placeholder'),
                            'description' => __('contenttypes::inputs.twitter.description'),
                            'value'       => $valueFromDB,
                        ],
                    ])->render(); 
                },
                'update'    => function($oldValues, $valueFromRequest) { 
                    app(\Modules\SystemCore\Entities\Contracts\SystemConfigInterface::class)->create([
                        'key'   => $valueFromRequest['key'],
                        'value' => $valueFromRequest['val'],
                    ]);
                    return true; 
                },
                'show'      => function() { return true; },
            ]);
            app(\Modules\SystemCore\Entities\Contracts\SystemConfigInterface::class)->register([
                'ordering'  => 0,
                'key'       => 'instagram',
                'input'     => function($valueFromDB) { 
                    return view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'text',
                            'name'        => 'config[instagram][val]',
                            'label'       => __('contenttypes::inputs.instagram.label'),
                            'placeholder' => __('contenttypes::inputs.instagram.placeholder'),
                            'description' => __('contenttypes::inputs.instagram.description'),
                            'value'       => $valueFromDB,
                        ],
                    ])->render(); 
                },
                'update'    => function($oldValues, $valueFromRequest) { 
                    app(\Modules\SystemCore\Entities\Contracts\SystemConfigInterface::class)->create([
                        'key'   => $valueFromRequest['key'],
                        'value' => $valueFromRequest['val'],
                    ]);
                    return true; 
                },
                'show'      => function() { return true; },
            ]);
            app(\Modules\SystemCore\Entities\Contracts\SystemConfigInterface::class)->register([
                'ordering'  => 0,
                'key'       => 'youtube',
                'input'     => function($valueFromDB) { 
                    return view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'text',
                            'name'        => 'config[youtube][val]',
                            'label'       => __('contenttypes::inputs.youtube.label'),
                            'placeholder' => __('contenttypes::inputs.youtube.placeholder'),
                            'description' => __('contenttypes::inputs.youtube.description'),
                            'value'       => $valueFromDB,
                        ],
                    ])->render(); 
                },
                'update'    => function($oldValues, $valueFromRequest) { 
                    app(\Modules\SystemCore\Entities\Contracts\SystemConfigInterface::class)->create([
                        'key'   => $valueFromRequest['key'],
                        'value' => $valueFromRequest['val'],
                    ]);
                    return true; 
                },
                'show'      => function() { return true; },
            ]);
            app(\Modules\SystemCore\Entities\Contracts\SystemConfigInterface::class)->register([
                'ordering'  => 0,
                'key'       => 'address',
                'input'     => function($valueFromDB) { 
                    return view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'textarea',
                            'name'        => 'config[address][val]',
                            'label'       => __('contenttypes::inputs.address.label'),
                            'placeholder' => __('contenttypes::inputs.address.placeholder'),
                            'description' => __('contenttypes::inputs.address.description'),
                            'value'       => $valueFromDB,
                        ],
                    ])->render(); 
                },
                'update'    => function($oldValues, $valueFromRequest) { 
                    app(\Modules\SystemCore\Entities\Contracts\SystemConfigInterface::class)->create([
                        'key'   => $valueFromRequest['key'],
                        'value' => $valueFromRequest['val'],
                    ]);
                    return true; 
                },
                'show'      => function() { return true; },
            ]);

            // privacy-policy
            app(\Modules\ContentManager\Entities\Contracts\SectionInterface::class)->register([
                'title'       => __('contenttypes::strings.privacy-policy.title'),
                'description' => __('contenttypes::strings.privacy-policy.description'),
                'tag'         => 'privacy-policy',
                'form'        => function($locale, $valueFromDB) { 
                    return 
                    view('ui::components.input', [
                        'options' => [
                            'rows'        => 6,
                            'view'        => 'DEFAULT',
                            'type'        => 'text',
                            'name'        => 'sections[privacy-policy][title]['. $locale .']',
                            'label'       => __('contenttypes::inputs.title.label') . '<span class="text-primary"> [' . $locale . ']</span>',
                            'placeholder' => __('contenttypes::inputs.title.placeholder'),
                            'description' => __('contenttypes::inputs.title.description'),
                            'value'       => ! isset($valueFromDB['title']) ? null : ( ! isset($valueFromDB['title'][$locale]) ? null : $valueFromDB['title'][$locale]),
                        ],
                    ])->render().
                    view('ui::components.editor', [
                        'options' => [
                            'rows'        => 30,
                            'view'        => 'DEFAULT',
                            'type'        => 'text',
                            'name'        => 'sections[privacy-policy][content]['. $locale .']',
                            'label'       => __('contenttypes::inputs.content.label') . '<span class="text-primary"> [' . $locale . ']</span>',
                            'placeholder' => __('contenttypes::inputs.content.placeholder'),
                            'description' => __('contenttypes::inputs.content.description'),
                            'value'       => ! isset($valueFromDB['content']) ? null : ( ! isset($valueFromDB['content'][$locale]) ? null : $valueFromDB['content'][$locale]),
                        ],
                    ])->render(); 
                },
                'update'    => function($oldValues, $valueFromRequest) { 
                    app(\Modules\ContentManager\Entities\Contracts\SectionInterface::class)->insert('privacy-policy', $valueFromRequest);
                },
                'show'      => function() { return true; },
            ]);

            // terms-conditions
            app(\Modules\ContentManager\Entities\Contracts\SectionInterface::class)->register([
                'title'       => __('contenttypes::strings.terms-conditions.title'),
                'description' => __('contenttypes::strings.terms-conditions.description'),
                'tag'         => 'terms-conditions',
                'form'        => function($locale, $valueFromDB) { 
                    return 
                    view('ui::components.input', [
                        'options' => [
                            'rows'        => 6,
                            'view'        => 'DEFAULT',
                            'type'        => 'text',
                            'name'        => 'sections[terms-conditions][title]['. $locale .']',
                            'label'       => __('contenttypes::inputs.title.label') . '<span class="text-primary"> [' . $locale . ']</span>',
                            'placeholder' => __('contenttypes::inputs.title.placeholder'),
                            'description' => __('contenttypes::inputs.title.description'),
                            'value'       => ! isset($valueFromDB['title']) ? null : ( ! isset($valueFromDB['title'][$locale]) ? null : $valueFromDB['title'][$locale]),
                        ],
                    ])->render().
                    view('ui::components.editor', [
                        'options' => [
                            'rows'        => 30,
                            'view'        => 'DEFAULT',
                            'type'        => 'text',
                            'name'        => 'sections[terms-conditions][content]['. $locale .']',
                            'label'       => __('contenttypes::inputs.content.label') . '<span class="text-primary"> [' . $locale . ']</span>',
                            'placeholder' => __('contenttypes::inputs.content.placeholder'),
                            'description' => __('contenttypes::inputs.content.description'),
                            'value'       => ! isset($valueFromDB['content']) ? null : ( ! isset($valueFromDB['content'][$locale]) ? null : $valueFromDB['content'][$locale]),
                        ],
                    ])->render(); 
                },
                'update'    => function($oldValues, $valueFromRequest) { 
                    app(\Modules\ContentManager\Entities\Contracts\SectionInterface::class)->insert('terms-conditions', $valueFromRequest);
                },
                'show'      => function() { return true; },
            ]);

            app('Ui')->registerMainMenuItem([
                'id'        => 'Pre Defined Lists',
                'ordering'  => 8000,
                'type'      => 'HEADER', //MENU | HEADER
                'text'      => __('contenttypes::strings.global.Pre Defined Lists'),
                'show'      => function() { return true; },
            ]);
            
            // profile-type
            app(\Modules\ContentManager\Entities\Contracts\CategoryInterface::class)->register([
                'ordering'  => 0,
                'base_type' => 'CATEGORY',
                'type'      => 'profile-type',
                'menu'      => __('contenttypes::strings.profile-type.menu'),
                'list'      => __('contenttypes::strings.profile-type.list'),
                'create'    => __('contenttypes::strings.profile-type.create'),
                'update'    => __('contenttypes::strings.profile-type.update'),
                'show'      => function() { return true; },
                'inputs'    => [],
            ]);
            app('Ui')->registerMainMenuItem([
                'id'        => 'profile-type/categories@index',
                'parent_id' => 'Pre Defined Lists',
                'ordering'  => 0,
                'type'      => 'MENU', //MENU | HEADER
                'link'      => route('CategoryController@index', ['type' => 'profile-type']),
                'text'      => __('contenttypes::strings.profile-type.menu'),
                'icon'      => app('Ui')->defaultIcon(),
                'show'      => function() { return true; },
            ]);

            // registration-type
            app(\Modules\ContentManager\Entities\Contracts\CategoryInterface::class)->register([
                'ordering'  => 0,
                'base_type' => 'CATEGORY',
                'type'      => 'registration-type',
                'menu'      => __('contenttypes::strings.registration-type.menu'),
                'list'      => __('contenttypes::strings.registration-type.list'),
                'create'    => __('contenttypes::strings.registration-type.create'),
                'update'    => __('contenttypes::strings.registration-type.update'),
                'show'      => function() { return true; },
                'inputs'    => [],
            ]);
            app('Ui')->registerMainMenuItem([
                'id'        => 'registration-type/categories@index',
                'parent_id' => 'Pre Defined Lists',
                'ordering'  => 0,
                'type'      => 'MENU', //MENU | HEADER
                'link'      => route('CategoryController@index', ['type' => 'registration-type']),
                'text'      => __('contenttypes::strings.registration-type.menu'),
                'icon'      => app('Ui')->defaultIcon(),
                'show'      => function() { return true; },
            ]);

            // payment-term
            app(\Modules\ContentManager\Entities\Contracts\CategoryInterface::class)->register([
                'ordering'  => 0,
                'base_type' => 'CATEGORY',
                'type'      => 'payment-term',
                'menu'      => __('contenttypes::strings.payment-term.menu'),
                'list'      => __('contenttypes::strings.payment-term.list'),
                'create'    => __('contenttypes::strings.payment-term.create'),
                'update'    => __('contenttypes::strings.payment-term.update'),
                'show'      => function() { return true; },
                'inputs'    => [],
            ]);
            app('Ui')->registerMainMenuItem([
                'id'        => 'payment-term/categories@index',
                'parent_id' => 'Pre Defined Lists',
                'ordering'  => 0,
                'type'      => 'MENU', //MENU | HEADER
                'link'      => route('CategoryController@index', ['type' => 'payment-term']),
                'text'      => __('contenttypes::strings.payment-term.menu'),
                'icon'      => app('Ui')->defaultIcon(),
                'show'      => function() { return true; },
            ]);

            // commodity
            app(\Modules\ContentManager\Entities\Contracts\CategoryInterface::class)->register([
                'ordering'  => 0,
                'base_type' => 'CATEGORY',
                'type'      => 'commodity',
                'menu'      => __('contenttypes::strings.commodity.menu'),
                'list'      => __('contenttypes::strings.commodity.list'),
                'create'    => __('contenttypes::strings.commodity.create'),
                'update'    => __('contenttypes::strings.commodity.update'),
                'show'      => function() { return true; },
                'inputs'    => [],
            ]);
            app('Ui')->registerMainMenuItem([
                'id'        => 'commodity/categories@index',
                'parent_id' => 'Pre Defined Lists',
                'ordering'  => 0,
                'type'      => 'MENU', //MENU | HEADER
                'link'      => route('CategoryController@index', ['type' => 'commodity']),
                'text'      => __('contenttypes::strings.commodity.menu'),
                'icon'      => app('Ui')->defaultIcon(),
                'show'      => function() { return true; },
            ]);

            // truck-type
            app(\Modules\ContentManager\Entities\Contracts\CategoryInterface::class)->register([
                'ordering'  => 0,
                'base_type' => 'CATEGORY',
                'type'      => 'truck-type',
                'menu'      => __('contenttypes::strings.truck-type.menu'),
                'list'      => __('contenttypes::strings.truck-type.list'),
                'create'    => __('contenttypes::strings.truck-type.create'),
                'update'    => __('contenttypes::strings.truck-type.update'),
                'show'      => function() { return true; },
                'inputs'    => [],
            ]);
            app('Ui')->registerMainMenuItem([
                'id'        => 'truck-type/categories@index',
                'parent_id' => 'Pre Defined Lists',
                'ordering'  => 0,
                'type'      => 'MENU', //MENU | HEADER
                'link'      => route('CategoryController@index', ['type' => 'truck-type']),
                'text'      => __('contenttypes::strings.truck-type.menu'),
                'icon'      => app('Ui')->defaultIcon(),
                'show'      => function() { return true; },
            ]);

            // load-size
            app(\Modules\ContentManager\Entities\Contracts\CategoryInterface::class)->register([
                'ordering'  => 0,
                'base_type' => 'CATEGORY',
                'type'      => 'load-size',
                'menu'      => __('contenttypes::strings.load-size.menu'),
                'list'      => __('contenttypes::strings.load-size.list'),
                'create'    => __('contenttypes::strings.load-size.create'),
                'update'    => __('contenttypes::strings.load-size.update'),
                'show'      => function() { return true; },
                'inputs'    => [],
            ]);
            app('Ui')->registerMainMenuItem([
                'id'        => 'load-size/categories@index',
                'parent_id' => 'Pre Defined Lists',
                'ordering'  => 0,
                'type'      => 'MENU', //MENU | HEADER
                'link'      => route('CategoryController@index', ['type' => 'load-size']),
                'text'      => __('contenttypes::strings.load-size.menu'),
                'icon'      => app('Ui')->defaultIcon(),
                'show'      => function() { return true; },
            ]);

            // load-type
            app(\Modules\ContentManager\Entities\Contracts\CategoryInterface::class)->register([
                'ordering'  => 0,
                'base_type' => 'CATEGORY',
                'type'      => 'load-type',
                'menu'      => __('contenttypes::strings.load-type.menu'),
                'list'      => __('contenttypes::strings.load-type.list'),
                'create'    => __('contenttypes::strings.load-type.create'),
                'update'    => __('contenttypes::strings.load-type.update'),
                'show'      => function() { return true; },
                'inputs'    => [],
            ]);
            app('Ui')->registerMainMenuItem([
                'id'        => 'load-type/categories@index',
                'parent_id' => 'Pre Defined Lists',
                'ordering'  => 0,
                'type'      => 'MENU', //MENU | HEADER
                'link'      => route('CategoryController@index', ['type' => 'load-type']),
                'text'      => __('contenttypes::strings.load-type.menu'),
                'icon'      => app('Ui')->defaultIcon(),
                'show'      => function() { return true; },
            ]);
        });
    }
}
