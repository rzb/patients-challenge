<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

class MacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->strRemoveNonDigits();
    }

    protected function strRemoveNonDigits(): void
    {
        Str::macro('removeNonDigits', fn ($value) =>
            preg_replace('/[^0-9]/', '', $value)
        );

        Stringable::macro('removeNonDigits', function () {
            return new Stringable(Str::removeNonDigits($this->value));
        });
    }
}
