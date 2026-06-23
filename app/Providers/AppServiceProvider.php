<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Policies\InvoicePolicy;
use App\Models\Invoice;

use App\Models\Client;
use App\Policies\ClientPolicy;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Invoice::class, InvoicePolicy::class);
        Gate::policy(Client::class, ClientPolicy::class);
    }
}
