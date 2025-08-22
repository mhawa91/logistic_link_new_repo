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

            $isRootOrAdmin = function() {
                return auth()->check() && (auth()->user()->isAn('ROOT') || auth()->user()->isAn('SYSTEM_ADMIN'));
            };
            $isConsumer = function() {
                return auth()->check() && (auth()->user()->isAn('DEFAULT') || auth()->user()->isAn('TRANSPORTER'));
            };
            $isClient = function() {
                return auth()->check() && (auth()->user()->isAn('DEFAULT'));
            };

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
                'show'      => $isRootOrAdmin,
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
                'show'      => $isRootOrAdmin,
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
                'show'      => $isRootOrAdmin,
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
                'show'      => $isRootOrAdmin,
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
                'show'      => $isRootOrAdmin,
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
                'show'      => $isRootOrAdmin,
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
                'show'      => $isRootOrAdmin,
            ]);
            app(\Modules\SystemCore\Entities\Contracts\SystemConfigInterface::class)->register([
                'ordering'  => 0,
                'key'       => 'address_iframe',
                'input'     => function($valueFromDB) { 
                    return view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'textarea',
                            'name'        => 'config[address_iframe][val]',
                            'label'       => __('contenttypes::inputs.address_iframe.label'),
                            'placeholder' => __('contenttypes::inputs.address_iframe.placeholder'),
                            'description' => __('contenttypes::inputs.address_iframe.description'),
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
                'show'      => $isRootOrAdmin,
            ]);

            // home-hero
            app(\Modules\ContentManager\Entities\Contracts\SectionInterface::class)->register([
                'title'       => __('contenttypes::strings.home-hero.title'),
                'description' => __('contenttypes::strings.home-hero.description'),
                'tag'         => 'home-hero',
                'form'        => function($locale, $valueFromDB) { 
                    return 
                    view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'text',
                            'name'        => 'sections[home-hero][title]['. $locale .']',
                            'label'       => __('contenttypes::inputs.section_title.label') . '<span class="text-primary"> [' . $locale . ']</span>',
                            'placeholder' => __('contenttypes::inputs.section_title.placeholder'),
                            'description' => __('contenttypes::inputs.section_title.description'),
                            'value'       => ! isset($valueFromDB['title']) ? null : ( ! isset($valueFromDB['title'][$locale]) ? null : $valueFromDB['title'][$locale]),
                        ],
                    ])->render()
                    .
                    view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'textarea',
                            'name'        => 'sections[home-hero][content]['. $locale .']',
                            'label'       => __('contenttypes::inputs.section_content.label') . '<span class="text-primary"> [' . $locale . ']</span>',
                            'placeholder' => __('contenttypes::inputs.section_content.placeholder'),
                            'description' => __('contenttypes::inputs.section_content.description'),
                            'value'       => ! isset($valueFromDB['content']) ? null : ( ! isset($valueFromDB['content'][$locale]) ? null : $valueFromDB['content'][$locale]),
                        ],
                    ])->render()
                    . '<hr class="preview-hr">' .
                    view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'text',
                            'name'        => 'sections[home-hero][button-text]['. $locale .']',
                            'label'       => __('contenttypes::inputs.hero-button.label') . '<span class="text-primary"> [' . $locale . ']</span>',
                            'placeholder' => __('contenttypes::inputs.hero-button.placeholder'),
                            'description' => __('contenttypes::inputs.hero-button.description'),
                            'value'       => ! isset($valueFromDB['button-text']) ? null : ( ! isset($valueFromDB['button-text'][$locale]) ? null : $valueFromDB['button-text'][$locale]),
                        ],
                    ])->render()
                    .
                    view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'text',
                            'name'        => 'sections[home-hero][button-link]['. $locale .']',
                            'label'       => __('contenttypes::inputs.hero-button-link.label') . '<span class="text-primary"> [' . $locale . ']</span>',
                            'placeholder' => __('contenttypes::inputs.hero-button-link.placeholder'),
                            'description' => __('contenttypes::inputs.hero-button-link.description'),
                            'value'       => ! isset($valueFromDB['button-link']) ? null : ( ! isset($valueFromDB['button-link'][$locale]) ? null : $valueFromDB['button-link'][$locale]),
                        ],
                    ])->render()
                    ; 
                },
                'update'    => function($oldValues, $valueFromRequest) { 
                    app(\Modules\ContentManager\Entities\Contracts\SectionInterface::class)->insert('home-hero', $valueFromRequest);
                },
                'show'      => function() { return true; },
            ]);

            // home-features
            app(\Modules\ContentManager\Entities\Contracts\SectionInterface::class)->register([
                'title'       => __('contenttypes::strings.home-features.title'),
                'description' => __('contenttypes::strings.home-features.description'),
                'tag'         => 'home-features',
                'form'        => function($locale, $valueFromDB) { 
                    return 
                    view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'text',
                            'name'        => 'sections[home-features][title]['. $locale .']',
                            'label'       => __('contenttypes::inputs.section_title.label') . '<span class="text-primary"> [' . $locale . ']</span>',
                            'placeholder' => __('contenttypes::inputs.section_title.placeholder'),
                            'description' => __('contenttypes::inputs.section_title.description'),
                            'value'       => ! isset($valueFromDB['title']) ? null : ( ! isset($valueFromDB['title'][$locale]) ? null : $valueFromDB['title'][$locale]),
                        ],
                    ])->render()
                    .
                    view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'textarea',
                            'name'        => 'sections[home-features][content]['. $locale .']',
                            'label'       => __('contenttypes::inputs.section_content.label') . '<span class="text-primary"> [' . $locale . ']</span>',
                            'placeholder' => __('contenttypes::inputs.section_content.placeholder'),
                            'description' => __('contenttypes::inputs.section_content.description'),
                            'value'       => ! isset($valueFromDB['content']) ? null : ( ! isset($valueFromDB['content'][$locale]) ? null : $valueFromDB['content'][$locale]),
                        ],
                    ])->render()
                    . '<hr class="preview-hr">' .
                    view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'text',
                            'name'        => 'sections[home-features][feature-1-title]['. $locale .']',
                            'label'       => __('contenttypes::inputs.feature-title.label')       . ' (1)' . '<span class="text-primary"> [' . $locale . ']</span>',
                            'placeholder' => __('contenttypes::inputs.feature-title.placeholder') . ' (1)',
                            'description' => __('contenttypes::inputs.feature-title.description') . ' (1)',
                            'value'       => ! isset($valueFromDB['feature-1-title']) ? null : ( ! isset($valueFromDB['feature-1-title'][$locale]) ? null : $valueFromDB['feature-1-title'][$locale]),
                        ],
                    ])->render()
                    .
                    view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'textarea',
                            'name'        => 'sections[home-features][feature-1-content]['. $locale .']',
                            'label'       => __('contenttypes::inputs.feature-content.label')       . ' (1)' . '<span class="text-primary"> [' . $locale . ']</span>',
                            'placeholder' => __('contenttypes::inputs.feature-content.placeholder') . ' (1)',
                            'description' => __('contenttypes::inputs.feature-content.description') . ' (1)',
                            'value'       => ! isset($valueFromDB['feature-1-content']) ? null : ( ! isset($valueFromDB['feature-1-content'][$locale]) ? null : $valueFromDB['feature-1-content'][$locale]),
                        ],
                    ])->render()
                    . '<hr class="preview-hr">' .
                    view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'text',
                            'name'        => 'sections[home-features][feature-2-title]['. $locale .']',
                            'label'       => __('contenttypes::inputs.feature-title.label')       . ' (2)' . '<span class="text-primary"> [' . $locale . ']</span>',
                            'placeholder' => __('contenttypes::inputs.feature-title.placeholder') . ' (2)',
                            'description' => __('contenttypes::inputs.feature-title.description') . ' (2)',
                            'value'       => ! isset($valueFromDB['feature-2-title']) ? null : ( ! isset($valueFromDB['feature-2-title'][$locale]) ? null : $valueFromDB['feature-2-title'][$locale]),
                        ],
                    ])->render()
                    .
                    view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'textarea',
                            'name'        => 'sections[home-features][feature-2-content]['. $locale .']',
                            'label'       => __('contenttypes::inputs.feature-content.label')       . ' (2)' . '<span class="text-primary"> [' . $locale . ']</span>',
                            'placeholder' => __('contenttypes::inputs.feature-content.placeholder') . ' (2)',
                            'description' => __('contenttypes::inputs.feature-content.description') . ' (2)',
                            'value'       => ! isset($valueFromDB['feature-2-content']) ? null : ( ! isset($valueFromDB['feature-2-content'][$locale]) ? null : $valueFromDB['feature-2-content'][$locale]),
                        ],
                    ])->render()
                    . '<hr class="preview-hr">' .
                    view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'text',
                            'name'        => 'sections[home-features][feature-3-title]['. $locale .']',
                            'label'       => __('contenttypes::inputs.feature-title.label')       . ' (3)' . '<span class="text-primary"> [' . $locale . ']</span>',
                            'placeholder' => __('contenttypes::inputs.feature-title.placeholder') . ' (3)',
                            'description' => __('contenttypes::inputs.feature-title.description') . ' (3)',
                            'value'       => ! isset($valueFromDB['feature-3-title']) ? null : ( ! isset($valueFromDB['feature-3-title'][$locale]) ? null : $valueFromDB['feature-3-title'][$locale]),
                        ],
                    ])->render()
                    .
                    view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'textarea',
                            'name'        => 'sections[home-features][feature-3-content]['. $locale .']',
                            'label'       => __('contenttypes::inputs.feature-content.label')       . ' (3)' . '<span class="text-primary"> [' . $locale . ']</span>',
                            'placeholder' => __('contenttypes::inputs.feature-content.placeholder') . ' (3)',
                            'description' => __('contenttypes::inputs.feature-content.description') . ' (3)',
                            'value'       => ! isset($valueFromDB['feature-3-content']) ? null : ( ! isset($valueFromDB['feature-3-content'][$locale]) ? null : $valueFromDB['feature-3-content'][$locale]),
                        ],
                    ])->render()
                    . '<hr class="preview-hr">' .
                    view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'text',
                            'name'        => 'sections[home-features][feature-4-title]['. $locale .']',
                            'label'       => __('contenttypes::inputs.feature-title.label')       . ' (4)' . '<span class="text-primary"> [' . $locale . ']</span>',
                            'placeholder' => __('contenttypes::inputs.feature-title.placeholder') . ' (4)',
                            'description' => __('contenttypes::inputs.feature-title.description') . ' (4)',
                            'value'       => ! isset($valueFromDB['feature-4-title']) ? null : ( ! isset($valueFromDB['feature-4-title'][$locale]) ? null : $valueFromDB['feature-4-title'][$locale]),
                        ],
                    ])->render()
                    .
                    view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'textarea',
                            'name'        => 'sections[home-features][feature-4-content]['. $locale .']',
                            'label'       => __('contenttypes::inputs.feature-content.label')       . ' (4)' . '<span class="text-primary"> [' . $locale . ']</span>',
                            'placeholder' => __('contenttypes::inputs.feature-content.placeholder') . ' (4)',
                            'description' => __('contenttypes::inputs.feature-content.description') . ' (4)',
                            'value'       => ! isset($valueFromDB['feature-4-content']) ? null : ( ! isset($valueFromDB['feature-4-content'][$locale]) ? null : $valueFromDB['feature-4-content'][$locale]),
                        ],
                    ])->render()
                    . '<hr class="preview-hr">' .
                    view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'text',
                            'name'        => 'sections[home-features][feature-5-title]['. $locale .']',
                            'label'       => __('contenttypes::inputs.feature-title.label')       . ' (5)' . '<span class="text-primary"> [' . $locale . ']</span>',
                            'placeholder' => __('contenttypes::inputs.feature-title.placeholder') . ' (5)',
                            'description' => __('contenttypes::inputs.feature-title.description') . ' (5)',
                            'value'       => ! isset($valueFromDB['feature-5-title']) ? null : ( ! isset($valueFromDB['feature-5-title'][$locale]) ? null : $valueFromDB['feature-5-title'][$locale]),
                        ],
                    ])->render()
                    .
                    view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'textarea',
                            'name'        => 'sections[home-features][feature-5-content]['. $locale .']',
                            'label'       => __('contenttypes::inputs.feature-content.label')       . ' (5)' . '<span class="text-primary"> [' . $locale . ']</span>',
                            'placeholder' => __('contenttypes::inputs.feature-content.placeholder') . ' (5)',
                            'description' => __('contenttypes::inputs.feature-content.description') . ' (5)',
                            'value'       => ! isset($valueFromDB['feature-5-content']) ? null : ( ! isset($valueFromDB['feature-5-content'][$locale]) ? null : $valueFromDB['feature-5-content'][$locale]),
                        ],
                    ])->render()
                    . '<hr class="preview-hr">' .
                    view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'text',
                            'name'        => 'sections[home-features][feature-6-title]['. $locale .']',
                            'label'       => __('contenttypes::inputs.feature-title.label')       . ' (6)' . '<span class="text-primary"> [' . $locale . ']</span>',
                            'placeholder' => __('contenttypes::inputs.feature-title.placeholder') . ' (6)',
                            'description' => __('contenttypes::inputs.feature-title.description') . ' (6)',
                            'value'       => ! isset($valueFromDB['feature-6-title']) ? null : ( ! isset($valueFromDB['feature-6-title'][$locale]) ? null : $valueFromDB['feature-6-title'][$locale]),
                        ],
                    ])->render()
                    .
                    view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'textarea',
                            'name'        => 'sections[home-features][feature-6-content]['. $locale .']',
                            'label'       => __('contenttypes::inputs.feature-content.label')       . ' (6)' . '<span class="text-primary"> [' . $locale . ']</span>',
                            'placeholder' => __('contenttypes::inputs.feature-content.placeholder') . ' (6)',
                            'description' => __('contenttypes::inputs.feature-content.description') . ' (6)',
                            'value'       => ! isset($valueFromDB['feature-6-content']) ? null : ( ! isset($valueFromDB['feature-6-content'][$locale]) ? null : $valueFromDB['feature-6-content'][$locale]),
                        ],
                    ])->render()
                    ; 
                },
                'update'    => function($oldValues, $valueFromRequest) { 
                    app(\Modules\ContentManager\Entities\Contracts\SectionInterface::class)->insert('home-features', $valueFromRequest);
                },
                'show'      => function() { return true; },
            ]);

            // how-it-works
            app(\Modules\ContentManager\Entities\Contracts\SectionInterface::class)->register([
                'title'       => __('contenttypes::strings.how-it-works.title'),
                'description' => __('contenttypes::strings.how-it-works.description'),
                'tag'         => 'how-it-works',
                'form'        => function($locale, $valueFromDB) { 
                    return 
                    view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'text',
                            'name'        => 'sections[how-it-works][title]['. $locale .']',
                            'label'       => __('contenttypes::inputs.section_title.label') . '<span class="text-primary"> [' . $locale . ']</span>',
                            'placeholder' => __('contenttypes::inputs.section_title.placeholder'),
                            'description' => __('contenttypes::inputs.section_title.description'),
                            'value'       => ! isset($valueFromDB['title']) ? null : ( ! isset($valueFromDB['title'][$locale]) ? null : $valueFromDB['title'][$locale]),
                        ],
                    ])->render()
                    .
                    view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'textarea',
                            'name'        => 'sections[how-it-works][content]['. $locale .']',
                            'label'       => __('contenttypes::inputs.section_content.label') . '<span class="text-primary"> [' . $locale . ']</span>',
                            'placeholder' => __('contenttypes::inputs.section_content.placeholder'),
                            'description' => __('contenttypes::inputs.section_content.description'),
                            'value'       => ! isset($valueFromDB['content']) ? null : ( ! isset($valueFromDB['content'][$locale]) ? null : $valueFromDB['content'][$locale]),
                        ],
                    ])->render()
                    . '<hr class="preview-hr">' .
                    view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'textarea',
                            'name'        => 'sections[how-it-works][section-1]['. $locale .']',
                            'label'       => __('contenttypes::inputs.section_content.label')       . ' (1)' . '<span class="text-primary"> [' . $locale . ']</span>',
                            'placeholder' => __('contenttypes::inputs.section_content.placeholder') . ' (1)',
                            'description' => __('contenttypes::inputs.section_content.description') . ' (1)',
                            'value'       => ! isset($valueFromDB['section-1']) ? null : ( ! isset($valueFromDB['section-1'][$locale]) ? null : $valueFromDB['section-1'][$locale]),
                        ],
                    ])->render()
                    .
                    view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'textarea',
                            'name'        => 'sections[how-it-works][section-2]['. $locale .']',
                            'label'       => __('contenttypes::inputs.section_content.label')       . ' (2)' . '<span class="text-primary"> [' . $locale . ']</span>',
                            'placeholder' => __('contenttypes::inputs.section_content.placeholder') . ' (2)',
                            'description' => __('contenttypes::inputs.section_content.description') . ' (2)',
                            'value'       => ! isset($valueFromDB['section-2']) ? null : ( ! isset($valueFromDB['section-2'][$locale]) ? null : $valueFromDB['section-2'][$locale]),
                        ],
                    ])->render()
                    . '<hr class="preview-hr">' .
                    view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'text',
                            'name'        => 'sections[how-it-works][button-text]['. $locale .']',
                            'label'       => __('contenttypes::inputs.action-button.label') . '<span class="text-primary"> [' . $locale . ']</span>',
                            'placeholder' => __('contenttypes::inputs.action-button.placeholder'),
                            'description' => __('contenttypes::inputs.action-button.description'),
                            'value'       => ! isset($valueFromDB['button-text']) ? null : ( ! isset($valueFromDB['button-text'][$locale]) ? null : $valueFromDB['button-text'][$locale]),
                        ],
                    ])->render()
                    .
                    view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'text',
                            'name'        => 'sections[how-it-works][button-link]['. $locale .']',
                            'label'       => __('contenttypes::inputs.action-button-link.label') . '<span class="text-primary"> [' . $locale . ']</span>',
                            'placeholder' => __('contenttypes::inputs.action-button-link.placeholder'),
                            'description' => __('contenttypes::inputs.action-button-link.description'),
                            'value'       => ! isset($valueFromDB['button-link']) ? null : ( ! isset($valueFromDB['button-link'][$locale]) ? null : $valueFromDB['button-link'][$locale]),
                        ],
                    ])->render()
                    ; 
                },
                'update'    => function($oldValues, $valueFromRequest) { 
                    app(\Modules\ContentManager\Entities\Contracts\SectionInterface::class)->insert('how-it-works', $valueFromRequest);
                },
                'show'      => function() { return true; },
            ]);

            // testimonials
            app(\Modules\ContentManager\Entities\Contracts\SectionInterface::class)->register([
                'title'       => __('contenttypes::strings.testimonials.title'),
                'description' => __('contenttypes::strings.testimonials.description'),
                'tag'         => 'testimonials',
                'form'        => function($locale, $valueFromDB) { 
                    return 
                    view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'text',
                            'name'        => 'sections[testimonials][title]['. $locale .']',
                            'label'       => __('contenttypes::inputs.section_title.label') . '<span class="text-primary"> [' . $locale . ']</span>',
                            'placeholder' => __('contenttypes::inputs.section_title.placeholder'),
                            'description' => __('contenttypes::inputs.section_title.description'),
                            'value'       => ! isset($valueFromDB['title']) ? null : ( ! isset($valueFromDB['title'][$locale]) ? null : $valueFromDB['title'][$locale]),
                        ],
                    ])->render()
                    .
                    view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'textarea',
                            'name'        => 'sections[testimonials][content]['. $locale .']',
                            'label'       => __('contenttypes::inputs.section_content.label') . '<span class="text-primary"> [' . $locale . ']</span>',
                            'placeholder' => __('contenttypes::inputs.section_content.placeholder'),
                            'description' => __('contenttypes::inputs.section_content.description'),
                            'value'       => ! isset($valueFromDB['content']) ? null : ( ! isset($valueFromDB['content'][$locale]) ? null : $valueFromDB['content'][$locale]),
                        ],
                    ])->render()
                    ; 
                },
                'update'    => function($oldValues, $valueFromRequest) { 
                    app(\Modules\ContentManager\Entities\Contracts\SectionInterface::class)->insert('testimonials', $valueFromRequest);
                },
                'show'      => function() { return true; },
            ]);

            // faq-section
            app(\Modules\ContentManager\Entities\Contracts\SectionInterface::class)->register([
                'title'       => __('contenttypes::strings.faq-section.title'),
                'description' => __('contenttypes::strings.faq-section.description'),
                'tag'         => 'faq-section',
                'form'        => function($locale, $valueFromDB) { 
                    return 
                    view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'text',
                            'name'        => 'sections[faq-section][title]['. $locale .']',
                            'label'       => __('contenttypes::inputs.section_title.label') . '<span class="text-primary"> [' . $locale . ']</span>',
                            'placeholder' => __('contenttypes::inputs.section_title.placeholder'),
                            'description' => __('contenttypes::inputs.section_title.description'),
                            'value'       => ! isset($valueFromDB['title']) ? null : ( ! isset($valueFromDB['title'][$locale]) ? null : $valueFromDB['title'][$locale]),
                        ],
                    ])->render()
                    .
                    view('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'textarea',
                            'name'        => 'sections[faq-section][content]['. $locale .']',
                            'label'       => __('contenttypes::inputs.section_content.label') . '<span class="text-primary"> [' . $locale . ']</span>',
                            'placeholder' => __('contenttypes::inputs.section_content.placeholder'),
                            'description' => __('contenttypes::inputs.section_content.description'),
                            'value'       => ! isset($valueFromDB['content']) ? null : ( ! isset($valueFromDB['content'][$locale]) ? null : $valueFromDB['content'][$locale]),
                        ],
                    ])->render()
                    ; 
                },
                'update'    => function($oldValues, $valueFromRequest) { 
                    app(\Modules\ContentManager\Entities\Contracts\SectionInterface::class)->insert('faq-section', $valueFromRequest);
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
                'show'      => $isRootOrAdmin,
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
                'show'      => $isRootOrAdmin,
            ]);

            // testimonials [Content]
            app(\Modules\ContentManager\Entities\Contracts\ContentInterface::class)->register([
                'ordering'  => 0,
                'base_type' => 'POST',
                'type'      => 'testimonials',
                'menu'      => __('contenttypes::strings.testimonials.menu'),
                'list'      => __('contenttypes::strings.testimonials.list'),
                'create'    => __('contenttypes::strings.testimonials.create'),
                'update'    => __('contenttypes::strings.testimonials.update'),
                'show'      => function() { return true; },
                'inputs'    => [
                    'icon'          => false,
                    'sub_title'     => true,
                    'url'           => false,
                    'category'      => false,
                    'long_content'  => false,
                ],
            ]);

            // faq [Content]
            app(\Modules\ContentManager\Entities\Contracts\ContentInterface::class)->register([
                'ordering'  => 0,
                'base_type' => 'POST',
                'type'      => 'faq',
                'menu'      => __('contenttypes::strings.faq-section.menu'),
                'list'      => __('contenttypes::strings.faq-section.list'),
                'create'    => __('contenttypes::strings.faq-section.create'),
                'update'    => __('contenttypes::strings.faq-section.update'),
                'show'      => function() { return true; },
                'inputs'    => [
                    'image'         => false,
                    'icon'          => false,
                    'sub_title'     => false,
                    'url'           => false,
                    'category'      => false,
                    'long_content'  => false,
                ],
            ]);

            app('Ui')
            ->registerMainMenuItem([
                'id'        => 'testimonials',
                'parent_id' => 'ContentManager',
                'ordering'  => 2,
                'type'      => 'MENU', //MENU | HEADER
                'link'      => route('ContentController@index', ['type' => 'testimonials']),
                'text'      => __('contenttypes::strings.testimonials.menu'),
                'icon'      => app('Ui')->defaultIcon(),
                'show'      => $isRootOrAdmin,
            ])
            ->registerMainMenuItem([
                'id'        => 'testimonials@index',
                'parent_id' => 'testimonials',
                'ordering'  => 0,
                'type'      => 'MENU', //MENU | HEADER
                'link'      => route('ContentController@index', ['type' => 'testimonials']),
                'text'      => __('admin::strings.global.list_all'),
                'icon'      => app('Ui')->defaultIcon(),
                'show'      => $isRootOrAdmin,
            ])
            ->registerMainMenuItem([
                'id'        => 'testimonials@create',
                'parent_id' => 'testimonials',
                'ordering'  => 0,
                'type'      => 'MENU', //MENU | HEADER
                'link'      => route('ContentController@create', ['type' => 'testimonials']),
                'text'      => __('admin::strings.global.add_new'),
                'icon'      => app('Ui')->defaultIcon(),
                'show'      => $isRootOrAdmin,
            ]);

            app('Ui')
            ->registerMainMenuItem([
                'id'        => 'faq',
                'parent_id' => 'ContentManager',
                'ordering'  => 2,
                'type'      => 'MENU', //MENU | HEADER
                'link'      => route('ContentController@index', ['type' => 'faq']),
                'text'      => __('contenttypes::strings.faq-section.menu'),
                'icon'      => app('Ui')->defaultIcon(),
                'show'      => $isRootOrAdmin,
            ])
            ->registerMainMenuItem([
                'id'        => 'faq@index',
                'parent_id' => 'faq',
                'ordering'  => 0,
                'type'      => 'MENU', //MENU | HEADER
                'link'      => route('ContentController@index', ['type' => 'faq']),
                'text'      => __('admin::strings.global.list_all'),
                'icon'      => app('Ui')->defaultIcon(),
                'show'      => $isRootOrAdmin,
            ])
            ->registerMainMenuItem([
                'id'        => 'faq@create',
                'parent_id' => 'faq',
                'ordering'  => 0,
                'type'      => 'MENU', //MENU | HEADER
                'link'      => route('ContentController@create', ['type' => 'faq']),
                'text'      => __('admin::strings.global.add_new'),
                'icon'      => app('Ui')->defaultIcon(),
                'show'      => $isRootOrAdmin,
            ]);

            app('Ui')->registerMainMenuItem([
                'id'        => 'Pre Defined Lists',
                'ordering'  => 8000,
                'type'      => 'HEADER', //MENU | HEADER
                'text'      => __('contenttypes::strings.global.Pre Defined Lists'),
                'show'      => $isRootOrAdmin,
            ]);
            
            // profile-type
            // app(\Modules\ContentManager\Entities\Contracts\CategoryInterface::class)->register([
            //     'ordering'  => 0,
            //     'base_type' => 'CATEGORY',
            //     'type'      => 'profile-type',
            //     'menu'      => __('contenttypes::strings.profile-type.menu'),
            //     'list'      => __('contenttypes::strings.profile-type.list'),
            //     'create'    => __('contenttypes::strings.profile-type.create'),
            //     'update'    => __('contenttypes::strings.profile-type.update'),
            //     'show'      => $isRootOrAdmin,
            //     'inputs'    => [],
            // ]);
            // app('Ui')->registerMainMenuItem([
            //     'id'        => 'profile-type/categories@index',
            //     'parent_id' => 'Pre Defined Lists',
            //     'ordering'  => 0,
            //     'type'      => 'MENU', //MENU | HEADER
            //     'link'      => route('CategoryController@index', ['type' => 'profile-type']),
            //     'text'      => __('contenttypes::strings.profile-type.menu'),
            //     'icon'      => app('Ui')->defaultIcon(),
            //     'show'      => $isRootOrAdmin,
            // ]);

            // registration-type
            app(\Modules\ContentManager\Entities\Contracts\CategoryInterface::class)->register([
                'ordering'  => 0,
                'base_type' => 'CATEGORY',
                'type'      => 'registration-type',
                'menu'      => __('contenttypes::strings.registration-type.menu'),
                'list'      => __('contenttypes::strings.registration-type.list'),
                'create'    => __('contenttypes::strings.registration-type.create'),
                'update'    => __('contenttypes::strings.registration-type.update'),
                'show'      => $isRootOrAdmin,
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
                'show'      => $isRootOrAdmin,
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
                'show'      => $isRootOrAdmin,
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
                'show'      => $isRootOrAdmin,
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
                'show'      => $isRootOrAdmin,
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
                'show'      => $isRootOrAdmin,
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
                'show'      => $isRootOrAdmin,
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
                'show'      => $isRootOrAdmin,
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
                'show'      => $isRootOrAdmin,
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
                'show'      => $isRootOrAdmin,
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
                'show'      => $isRootOrAdmin,
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
                'show'      => $isRootOrAdmin,
            ]);

            app('Ui')->registerMainMenuItem([
                'id'        => 'consumer/orders',
                'parent_id' => 'AdminDashboardController',
                'ordering'  => 0,
                'type'      => 'MENU', //MENU | HEADER
                'link'      => route('ShippmentsController@index'),
                'text'      => __('contenttypes::strings.orders.menu'),
                'icon'      => [
                    'name' => 'DEFAULT',
                    'type' => 'em', //em | i | svg | span
                    'data' => 'icon ni ni-layers',
                ],
                'show'      => $isConsumer,
            ]);
            app('Ui')->registerMainMenuItem([
                'id'        => 'consumer/shippments_rfqs',
                'parent_id' => 'AdminDashboardController',
                'ordering'  => 0,
                'type'      => 'MENU', //MENU | HEADER
                'link'      => route('RfqController@index'),
                'text'      => __('contenttypes::strings.shippments_rfqs.menu'),
                'icon'      => [
                    'name' => 'DEFAULT',
                    'type' => 'em', //em | i | svg | span
                    'data' => 'icon ni ni-list-index',
                ],
                'show'      => $isClient,
            ]);
            app('Ui')->registerMainMenuItem([
                'id'        => 'consumer/transporters',
                'parent_id' => 'AdminDashboardController',
                'ordering'  => 0,
                'type'      => 'MENU', //MENU | HEADER
                'link'      => route('TransportersController@index'),
                'text'      => __('contenttypes::strings.contracts.menu'),
                'icon'      => [
                    'name' => 'DEFAULT',
                    'type' => 'em', //em | i | svg | span
                    'data' => 'icon ni ni-truck',
                ],
                'show'      => $isConsumer,
            ]);
            app('Ui')->registerMainMenuItem([
                'id'        => 'consumer/invoices',
                'parent_id' => 'AdminDashboardController',
                'ordering'  => 0,
                'type'      => 'MENU', //MENU | HEADER
                'link'      => route('InvoicesController@index'),
                'text'      => __('contenttypes::strings.finance_section.menu'),
                'icon'      => [
                    'name' => 'DEFAULT',
                    'type' => 'em', //em | i | svg | span
                    'data' => 'icon ni ni-tranx',
                ],
                'show'      => $isConsumer,
            ])
            ->registerMainMenuItem([
                'id'        => 'consumer/invoices/clients',
                'parent_id' => 'consumer/invoices',
                'ordering'  => 0,
                'type'      => 'MENU', //MENU | HEADER
                'link'      => route('InvoicesController@groupedIndex'),
                'text'      => __('contenttypes::strings.clients.menu'),
                'icon'      => app('Ui')->defaultIcon(),
                'show'      => $isConsumer,
            ])
            ->registerMainMenuItem([
                'id'        => 'consumer/invoices/list',
                'parent_id' => 'consumer/invoices',
                'ordering'  => 1,
                'type'      => 'MENU', //MENU | HEADER
                'link'      => route('InvoicesController@index'),
                'text'      => __('contenttypes::strings.invoices.menu'),
                'icon'      => app('Ui')->defaultIcon(),
                'show'      => $isConsumer,
            ]);


            app('Ui')->registerMainMenuItem([
                'id'        => 'consumer/reports',
                'parent_id' => 'AdminDashboardController',
                'ordering'  => 0,
                'type'      => 'MENU', //MENU | HEADER
                'link'      => 'javascript:void(0);',
                'text'      => __('contenttypes::strings.reports.menu'),
                'icon'      => [
                    'name' => 'DEFAULT',
                    'type' => 'em', //em | i | svg | span
                    'data' => 'icon ni ni-growth',
                ],
                'show'      => $isConsumer,
            ]);
            app('Ui')->registerMainMenuItem([
                'id'        => 'consumer/settings',
                'parent_id' => 'AdminDashboardController',
                'ordering'  => 0,
                'type'      => 'MENU', //MENU | HEADER
                'link'      => route('UsersController@profile'),
                'text'      => __('contenttypes::strings.settings.menu'),
                'icon'      => [
                    'name' => 'DEFAULT',
                    'type' => 'em', //em | i | svg | span
                    'data' => 'icon ni ni-setting-alt',
                ],
                'show'      => $isConsumer,
            ]);
        });
    }
}
