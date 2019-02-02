<?php namespace ewma\components\ui\controllers\main\cats;

class App extends \Controller
{
    public function getQueryBuilder()
    {
        return \ewma\components\models\Cat::orderBy('position');
    }

    public function moveCallback()
    {
        $cat = $this->data['cat'];

        // ...
    }

    public function sortCallback()
    {
        $cat = $this->data['cat'];

        // ...
    }

    public function export()
    {
        if ($cat = $this->unpackModel('cat')) {
            return components()->cats->export($cat);
        }
    }

    public function import()
    {
        if ($cat = $this->unpackModel('cat')) {
            components()->cats->import($cat, $this->data('data'), $this->data('skip_first_level'));

            $this->e('ewma/components/cats/import')->trigger();
        }
    }
}
