<?php namespace ewma\components\ui\component\controllers\main;

class CreateInstanceForm extends \Controller
{
    private $component;

    public function __create()
    {
        $this->component = $this->unpackModel('component') or $this->lock();
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $s = $this->s(false, [
            'value' => ''
        ]);

        $component = $this->component;
        $componentXPack = xpack_model($component);

        $used = false;
        if (components()->getHandler($component, $s['value'])) {
            $used = true;
        }

        $class = [];

        if ($used) {
            $class[] = 'used';
        } else {
            $class[] = 'free';
        }

        $v->assign([
                       'CLASS'       => implode(' ', $class),
                       'VALUE'       => $s['value'],
                       'SAVE_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:save|',
                           'data'    => [
                               'component' => $componentXPack
                           ],
                           'class'   => 'save_button ' . ($used ? 'disabled' : ''),
                           'content' => 'Сохранить'
                       ])
                   ]);

        $this->c('\std\ui liveinput:bind', [
            'selector' => $this->_selector('|') . ' input',
            'path'     => '>xhr:update|',
            'data'     => [
                'component' => $componentXPack
            ],
            'timeout'  => 100
        ]);

        $this->css(':\css\std~');

        return $v;
    }
}
