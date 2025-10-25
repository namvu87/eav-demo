<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Repository bindings
        $this->app->bind(
            \App\Repositories\AttributeRepository::class,
            \App\Repositories\AttributeRepositoryEloquent::class
        );
        
        $this->app->bind(
            \App\Repositories\EntityTypeRepository::class,
            \App\Repositories\EntityTypeRepositoryEloquent::class
        );
        
        $this->app->bind(
            \App\Repositories\EntityRepository::class,
            \App\Repositories\EntityRepositoryEloquent::class
        );
        
        // Service bindings (optional - Laravel auto-resolve via type-hint)
        // These are explicitly bound for clarity
        $this->app->singleton(\App\Services\AttributeService::class);
        $this->app->singleton(\App\Services\EntityTypeService::class);
        $this->app->singleton(\App\Services\EntityService::class);
        $this->app->singleton(\App\Services\EavService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
