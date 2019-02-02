<?php namespace ewma\components\ui\controllers\main\cats;

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

    private function getMessage()
    {
        $message = 'Категория <b>' . $this->data['cat_name'] . '</b> содержит ';

        $tmp = [];

        if ($this->data['nested_cats_count']) {
            $tmp[] = $this->data['nested_cats_count'] . ' подкатегори' . ending($this->data['nested_cats_count'], 'ю', 'и', 'й');

            $tail = 'Все подкатегории будут удалены.';
        }

        if ($this->data['components_count']) {
            $tmp[] = $this->data['components_count'] . ' компонент' . ending($this->data['components_count'], '', 'а', 'ов');

            $tail = 'Все компоненты будут удалены.';
        }

        if ($this->data['nested_cats_count'] && $this->data['components_count']) {
            $tail = 'Все подкатегории и компоненты будут удалены.';
        }

        // todo использующие компоненты

        if ($tmp) {
            $message .= implode(' и ', $tmp) . '.';
        }

        $message .= '<br>' . $tail;

        return $message;
    }
}
