<?php namespace ewma\components\ui\controllers\main\component;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function compile()
    {
        if ($component = $this->unxpackModel('component')) {
            components()->compile($component);
        }
    }
}
