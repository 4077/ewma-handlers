<?php namespace ewma\handlers\models;

class Cat extends \Model
{
    protected $table = 'ewma_handlers_cats';

    public function nested()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function handlers()
    {
        return $this->morphMany(Handler::class, 'target');
    }
}

class CatObserver
{
    public function creating($model)
    {
        $position = Cat::max('position') + 10;

        $model->position = $position;
    }
}

Cat::observe(new CatObserver);
