<?php

namespace Webkul\PurchaseOrder\Providers;

use Illuminate\Support\ServiceProvider;

class PurchaseOrderServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }
}
