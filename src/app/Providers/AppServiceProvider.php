<?php

namespace App\Providers;

use App\Clients\Cep\CepClient;
use App\Clients\Cep\ViaCep;
use Illuminate\Support\ServiceProvider;

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
        $this->app->bind(CepClient::class, ViaCep::class);
    }
}
