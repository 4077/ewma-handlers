<?php namespace ewma\handlers\models;

class Node extends \Model
{
    use \SleepingOwl\WithJoin\WithJoinTrait;

    protected $table = 'ewma_handlers_nodes';

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function nested()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function handler()
    {
        return $this->belongsTo(Handler::class);
    }
}

class NodeObserver
{
    public function creating($model)
    {
        $position = Node::where('handler_id', $model->handler_id)->max('position') + 10;

        $model->position = $position;
    }
}

Node::observe(new NodeObserver);
