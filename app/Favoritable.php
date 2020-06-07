<?php
namespace App;

trait Favoritable {

    protected static function bootFavoritable()
    {
        static::deleting(function ($model) {
            $model->favorites->each->delete();
        });
    }

    /**
     * get favorites according to favorite_type
     *
     * @return mixed
     */
    public function favorites()
    {
        return $this->morphMany('App\Favorite', 'favorited');
    }

    /**
     * favoritable can be favorited
     *
     * @return mixed
     */
    public function favorite()
    {
        $attributes = ['user_id' => auth()->id()];

        if (! $this->favorites()->where($attributes)->exists()) {
            return $this->favorites()->create($attributes);
        }
    }

    /**
     * unfavorite a favoritable
     *
     * @return void
     */
    public function unfavorite()
    {
        $attributes = ['user_id' => auth()->id()];

        return $this->favorites()->where($attributes)->get()->each->delete();
    }

    /**
     * check if the favoritable has been favorited by the user
     *
     * @return boolean
     */
    public function isFavorited()
    {
        return $this->favorites->where('user_id', auth()->id())->count() > 0;
    }

    /**
     * get IsFavorited
     *
     * @return bool
     */
    public function getIsFavoritedAttribute()
    {
        return $this->isFavorited();
    }

    /**
     * get favorites count
     *
     * @return int
     */
    public function getFavoritesCountAttribute()
    {
        return $this->favorites->count();
    }

}
