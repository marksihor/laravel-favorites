<?php

namespace MarksIhor\LaravelFavorites;

use Illuminate\Database\Eloquent\Model;
use MarksIhor\LaravelFavorites\Favorite;

trait Favoriteable
{
    public function favorites()
    {
        return $this->morphMany('App\Models\Favorite', 'favoriteable');
    }

    public function getFavorites(?string $type = null)
    {
        if ($type && !in_array($type, config('favorites.types'))) return 'Type not allowed.';

        $result = [];
        $favoritesGroupedByType = Favorite::where(function ($q) use ($type) {
            $q->where(['favoriteable_id' => $this->id, 'favoriteable_type' => $this->getMorphClass()]);

            if ($type) $q->where('favorite_type', array_flip(config('favorites.types'))[$type]);
        })->get()->groupBy('favorite_type');

        foreach ($favoritesGroupedByType as $type => $favorites) {
            $favoritesIds = $favorites->pluck('favorite_id');

            $result[config('favorites.types')[$type]] = $type::withoutGlobalScopes(config('favorites.without_scopes', []))->whereIn('id', $favoritesIds)->get();
        }

        return $result;
    }

    public function setFavorite(Model $model): string
    {
        if (!key_exists($model->getMorphClass(), config('favorites.types'))) return 'Type not allowed.';
        if ($this->checkIfInFavorite($model)) return 'Already in favorites.';

        Favorite::create($this->getFavoriteCredentials($model));

        return 'Added to favorites.';
    }

    public function unsetFavorite(Model $model): string
    {
        if (!$this->checkIfInFavorite($model)) return 'Not in favorite.';

        Favorite::where($this->getFavoriteCredentials($model))->delete();

        return 'Removed from favorites.';
    }

    public function checkIfInFavorite(Model $model): bool
    {
        return Favorite::where($this->getFavoriteCredentials($model))->exists();
    }

    private function getFavoriteCredentials(Model $model): array
    {
        return [
            'favoriteable_id' => $this->id, 'favoriteable_type' => $this->getMorphClass(), 'favorite_type' => $model->getMorphClass(), 'favorite_id' => $model->id
        ];
    }
}