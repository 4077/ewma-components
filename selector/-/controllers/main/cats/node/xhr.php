<?php namespace ewma\components\selector\controllers\main\cats\node;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function select()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $s = &$this->s('~|');

            $s['selected_cat_id'] = $cat->id;

            $this->e('ewma/components/selector/cat_select')->trigger();
        }
    }
}
