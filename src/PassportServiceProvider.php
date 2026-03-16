<?php

namespace Jundayw\Passport;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Jundayw\Passport\Contracts\Passport as PassportContract;

class PassportServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        if (!app()->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__.'/../config/passport.php', 'passport');
        }

        $this->app->singleton(PassportContract::class, function ($app) {
            return $app->make(Passport::class);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->registerMigrations();
            $this->registerPublishing();
        }
    }

    /**
     * Register the migration file.
     *
     * @return void
     */
    protected function registerMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing(): void
    {
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'passport-migrations');

        $this->publishes([
            __DIR__.'/../config/passport.php' => config_path('passport.php'),
        ], 'passport-config');
    }

}
