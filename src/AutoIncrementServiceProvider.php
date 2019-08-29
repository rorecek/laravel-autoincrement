<?php

namespace Rorecek\AutoIncrement;

use Illuminate\Support\ServiceProvider;

class AutoIncrementServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(AutoIncrement::class);
        $this->app->alias(AutoIncrement::class, 'auto-increment');
    }
}
