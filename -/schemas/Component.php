<?php namespace ewma\components\schemas;

class Component extends \Schema
{
    public $table = 'ewma_components';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('cat_id')->default(0);
            $table->integer('position')->default(0);
            $table->string('name')->default('');
        };
    }
}
