<?php namespace ewma\components\selector\controllers\main\cats;

class App extends \Controller
{
    public function getQueryBuilder()
    {
        return \ewma\components\models\Cat::orderBy('position');
    }
}
