<?php

namespace CptnHook\Integration\Laravel;

use Illuminate\Support\ServiceProvider;
use CptnHook\HookRepository;
use CptnHook\Integration\Laravel\EloquentHookRepository;
use CptnHook\Config as ConfigInterface;
use CptnHook\DefaultHookRunner;
use CptnHook\HookBuilder as HookBuilderInterface;
use CptnHook\Integration\Laravel\HookBuilder;
use CptnHook\Integration\Laravel\Command\RunHooksCommand;
use CptnHook\HookRunner;
use CptnHook\Integration\Laravel\Config;
use CtpnHook\DefaultFileSystem;
use CptnHook\FileSystem;

class CptnHookServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->publishes([
            __DIR__ . '/config/config.php' => config_path('cptnhook.php'),
        ]);
        
        if ($this->app->runningInConsole()) {
            $this->commands([
                RunHooksCommand::class,
            ]);
        }
        
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(HookRepository::class, config('cptnhook.repository', EloquentHookRepository::class));
        $this->app->bind(HookRunner::class, config('cptnhook.runner', DefaultHookRunner::class));
        $this->app->bind(FileSystem::class, config('cptnhook.filesystem', DefaultFileSystem::class));
        $this->app->bind(HookBuilderInterface::class, config('cptnhook.builder', HookBuilder::class));
        
        $this->app->bind(ConfigInterface::class, function ($app) {
            return new Config(config('cptnhook', []));
        });
    }
}
