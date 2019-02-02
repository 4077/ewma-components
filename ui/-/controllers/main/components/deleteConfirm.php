<?php namespace ewma\components\ui\controllers\main\components;

class DeleteConfirm extends \Controller
{
    public function view()
    {
        $v = $this->v();

        /**
         * @var $confirmCall \ewma\Controllers\Call
         * @var $discardCall \ewma\Controllers\Call
         */
        $confirmCall = $this->_call($this->data('confirm_call'));
        $discardCall = $this->_call($this->data('discard_call'));

        $confirmCall->data('confirmed', true);
        $discardCall->data('discarded', true);

        $v->assign([
                       'MESSAGE'        => $this->getMessage(),
                       'CONFIRM_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => $confirmCall->path(),
                           'data'    => $confirmCall->data(),
                           'class'   => 'button red',
                           'content' => 'Удалить'
                       ]),
                       'DISCARD_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => $discardCall->path(),
                           'data'    => $discardCall->data(),
                           'class'   => 'button blue',
                           'content' => 'Отмена'
                       ]),
                   ]);

        $this->css(':\css\std~');

        return $v;
    }

    private function getMessage() // todo
    {
        $component = $this->unpackModel('component');

        return 'Удалить компонент <b>' . ($component->name ? $component->name : '...') . '</b>?';
    }
}
