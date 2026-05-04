<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\InvoiceCalculationService;
use App\Services\InvoiceRegimeService;
use App\Services\InvoiceRemiseService;
use App\Services\InvoiceTvaService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
         $this->app->singleton(InvoiceRegimeService::class);
        $this->app->singleton(InvoiceTvaService::class);
        $this->app->singleton(InvoiceRemiseService::class);
        $this->app->singleton(InvoiceCalculationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
