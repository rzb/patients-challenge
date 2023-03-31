<?php

namespace App\Providers;

use App\Faker\Person;
use Faker\{Factory, Generator};
use Illuminate\Support\ServiceProvider;

class FakerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $locale = config('app.faker_locale');

        $generator = $this->generator($locale);

        $this->app->singleton(Generator::class, $generator);

        $this->app->singleton(Generator::class . ':' . $locale, $generator);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    protected function generator(string $locale): callable
    {
        return function () use ($locale) {
            $faker = Factory::create($locale);

            $faker->addProvider(Person::class);

            return $faker;
        };
    }
}
