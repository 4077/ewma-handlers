<?php namespace ewma\handlers\schemas;

class Node extends \Schema
{
    public $table = 'ewma_handlers_nodes';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('handler_id')->default(0);
            $table->integer('parent_id')->default(0);
            $table->integer('source_handler_id')->default(0); // todo del
            $table->integer('position')->default(0);
            $table->boolean('enabled')->default(false);
            $table->string('name')->default('');
            $table->enum('type', ['ROOT','CALL','INPUT','HANDLER','VALUE','DATA_MODIFIER'])->default('ROOT');
            $table->boolean('required')->default(false);
            $table->boolean('cache_enabled')->default(false); // todo not used
            $table->text('data');
            $table->text('mappings');
        };
    }
}
