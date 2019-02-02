<?php namespace ewma\components\selector\controllers\main\cats;

class Node extends \Controller
{
    private $cat;

    private $viewInstance;

    public function __create()
    {
        $this->cat = $this->data['cat'];

        $this->viewInstance = $this->cat->id;
    }

    public function view()
    {
        $v = $this->v('|' . $this->viewInstance);

        $cat = $this->cat;

        $v->assign([
                       'NAME' => $cat->name
                   ]);

        $this->c('\std\ui button:bind', [
            'selector' => $this->_selector('|' . $this->viewInstance),
            'path'     => '>xhr:select|',
            'data'     => [
                'cat' => xpack_model($cat)
            ]
        ]);

        $this->css();

        return $v;
    }
}
