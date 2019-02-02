<?php namespace ewma\components\ui\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function updateCatsWidth()
    {
        $this->s('~:cats_cp_width', $this->data('width'), RR);
    }
}
