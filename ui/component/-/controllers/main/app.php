<?php namespace ewma\components\ui\component\controllers\main;

class App extends \Controller
{
    public function export()
    {

    }

    public function import()
    {

    }

    public function setCat()
    {
        $cat = \ewma\components\models\Cat::find($this->data('cat_id'));
        $component = \ewma\components\models\Component::find($this->data('component_id'));

        if ($cat && $component) {
            $catIdBefore = $component->target_id;

            $component->cat()->associate($cat);
            $component->save();

            $this->e('ewma/components/update/cat', [
                'component_id' => $component->id,
                'cat_id'       => $catIdBefore
            ])->trigger(['component' => $component]);
        }
    }
}
