<?php namespace ewma\components\ui\controllers;

class Main extends \Controller
{
    public function __create()
    {
        $this->a('ewma\components:') or $this->lock();

        $this->s(false, [
            'selected_cat_id'                 => false,
            'selected_component_id_by_cat_id' => [],
            'cats_cp_width'                   => 250
        ]);
    }

    public function reload()
    {
        $this->jquery()->replace($this->view());
    }

    public function view()
    {
        $v = $this->v();
        $s = $this->s();

        $v->assign([
                       'CATS'          => $this->c('>cats:view'),
                       'CATS_CP_WIDTH' => $s['cats_cp_width'],
                       'COMPONENTS'    => $this->c('>components:view'),
                       'COMPONENT'     => $this->c('>component:view')
                   ]);

        $this->c('\std\ui resizable:bind', [
            'selector'      => $this->_selector('|') . ' .cats',
            'path'          => '>xhr:updateCatsWidth',
            'pluginOptions' => [
                'handles' => 'e'
            ]
        ]);

        $this->c('\std\ui\dialogs~:addContainer:ewma/handlers');
        $this->c('\std\ui\dialogs~:addContainer:ewma/components');

        $this->css();

        $this->app->html->setFavicon(abs_url('-/ewma/favicons/dev_components.png'));

        $this->e('ewma/components/cat_select')->rebind(':reload');

        return $v;
    }
}
