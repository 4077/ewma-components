<?php namespace ewma\components\selector\controllers\main\components;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function select()
    {
        if ($component = $this->unxpackModel('component')) {
            $this->dmap('~|', 'callbacks');

            if ($callback = $this->data('callbacks/select')) {
                $call = \ewma\Data\Data::tokenize($callback, [
                    '%component' => $component
                ]);

                $this->_call($call)->perform();
            }
        }
    }
}
