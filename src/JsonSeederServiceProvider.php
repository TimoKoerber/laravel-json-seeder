<?php

namespace TimoKoerber\LaravelJsonSeeder;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use TimoKoerber\LaravelJsonSeeder\Commands\JsonSeedsCreateCommand;
use TimoKoerber\LaravelJsonSeeder\Commands\JsonSeedsOverwriteCommand;

class JsonSeederServiceProvider extends IlluminateServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/jsonseeder.php' => config_path('jsonseeder.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands(JsonSeedsCreateCommand::class);
            $this->commands(JsonSeedsOverwriteCommand::class);
        }
    }
}
