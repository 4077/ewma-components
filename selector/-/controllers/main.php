<?php namespace ewma\components\selector\controllers;

class Main extends \Controller
{
    public function __create()
    {
        $this->s('|', [
            'selected_component_id' => false,
            'selected_cat_id'       => false,
            'cats_cp_width'         => 250
        ]);

        $this->smap('|', 'selected_component_id, available_cats_ids');
        $this->dmap('|', 'callbacks');
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');
        $s = $this->s('|');

        $v->assign([
                       'CATS'          => $this->c('>cats:view|'),
                       'CATS_CP_WIDTH' => $s['cats_cp_width'],
                       'COMPONENTS'    => $this->c('>components:view|')
                   ]);

        $this->c('\std\ui resizable:bind', [
            'selector'      => $this->_selector('|') . ' .cats_cp',
            'path'          => '>xhr:updateCatsWidth',
            'pluginOptions' => [
                'handles' => 'e'
            ]
        ]);

        $this->css();

        $this->e('ewma/components/selector/cat_select')->rebind(':reload|');

        return $v;
    }
}
