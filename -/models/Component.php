<?php namespace ewma\components\models;

class Component extends \Model
{
    protected $table = 'ewma_components';

    public function cat()
    {
        return $this->belongsTo(Cat::class);
    }

    public function handlers($instance = null)
    {
        $relation = $this->morphOne(\ewma\handlers\models\Handler::class, 'target');

        if (null !== $instance) {
            return $relation->where('instance', $instance);
        } else {
            return $relation;
        }
    }
}

class ComponentObserver
{
    public function creating($model)
    {
        $position = Component::max('position') + 10;

        $model->position = $position;
    }
}

Component::observe(new ComponentObserver);
