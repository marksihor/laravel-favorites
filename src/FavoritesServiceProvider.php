<?php

namespace MarksIhor\LaravelFavorites;

use Illuminate\Support\ServiceProvider;

class FavoritesServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/favorites.php' => config_path('favorites.php')
        ], 'favorites-config');

        $this->publishes([
            \dirname(__DIR__) . '/migrations/' => database_path('migrations'),
        ], 'favorites-migrations');
    }
}
