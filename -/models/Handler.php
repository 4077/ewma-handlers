<?php namespace ewma\handlers\models;

class Handler extends \Model
{
    protected $table = 'ewma_handlers';

    public function target()
    {
        return $this->morphTo();
    }

    public function cat()
    {
        return $this->belongsTo(Cat::class, 'target_id');
    }

    public function nodes()
    {
        return $this->hasMany(Node::class);
    }
}

class HandlerObserver
{
    public function creating($model)
    {
        $position = Handler::max('position') + 10;

        $model->position = $position;
    }
}

Handler::observe(new HandlerObserver);
