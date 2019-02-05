<?php namespace ewma\handlers\schemas;

class Handler extends \Schema
{
    public $table = 'ewma_handlers';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->morphs('target');
            $table->integer('cat_position')->default(0);
            $table->integer('position')->default(0);
            $table->string('instance')->default('');
            $table->string('name')->default('');
            $table->string('path')->default('');
        };
    }
}
