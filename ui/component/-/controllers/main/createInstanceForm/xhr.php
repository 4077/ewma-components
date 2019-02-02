<?php namespace ewma\components\ui\component\controllers\main\createInstanceForm;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function update()
    {
        if ($component = $this->unxpackModel('component')) {
            $newInstance = $this->data('value');

            $this->jquery($this->_selector('<:|') . ' input')->removeClass('used free');

            if (components()->getHandler($component, $newInstance)) {
                $this->jquery($this->_selector('<:|') . ' input')->addClass('used');
                $this->jquery($this->_selector('<:|') . ' .save_button')->addClass('disabled');
            } else {
                $this->jquery($this->_selector('<:|') . ' input')->addClass('free');
                $this->jquery($this->_selector('<:|') . ' .save_button')->removeClass('disabled');
            }

            $this->s('<:value', $newInstance, RR);
        }
    }

    public function save()
    {
        if ($component = $this->unxpackModel('component')) {
            $newInstance = $this->s('<:value');

            if (!components()->getHandler($component, $newInstance)) {
                components()->createHandler($component, $newInstance);

                $this->e('components/component/handlers/create')->trigger(['component' => $component]);

                $this->s('<:value', false, RR);
            }
        }
    }
}
