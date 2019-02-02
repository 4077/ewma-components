<?php namespace ewma\components\ui\controllers\main;

class Component extends \Controller
{
    public function reload()
    {
        $this->jquery()->replace($this->view());
    }

    public function view()
    {
        $v = $this->v();

        $s = $this->s('~');

        $selectedCatId = $s['selected_cat_id'];

        $componentId = $this->s('~:selected_component_id_by_cat_id/' . $selectedCatId);

        if ($component = \ewma\components\models\Component::find($componentId)) {
            $v->assign([
                           'COMPILE_BUTTON' => $this->c('\std\ui button:view', [
                               'path'    => '>xhr:compile',
                               'data'    => [
                                   'component' => xpack_model($component)
                               ],
                               'class'   => 'compile_button',
                               'content' => 'Скомпилировать'
                           ]),
                           'COMPONENT'      => $this->c('component~:view', [
                               'component' => $component
                           ])
                       ]);

            $this->e('ewma/components/cats/update/name', ['cat_id' => $component->target_id])->rebind(':reload');
            $this->e('ewma/components/update/name', ['component_id' => $component->id])->rebind(':reload');
        }

        $this->css(':\css\std~');

        $this->e('ewma/components/cat_select')->rebind(':reload');
        $this->e('ewma/components/select')->rebind(':reload');

        $this->e('ewma/components/cats/create')->rebind(':reload');
        $this->e('ewma/components/cats/delete')->rebind(':reload');

        $this->e('ewma/components/create')->rebind(':reload');
        $this->e('ewma/components/delete')->rebind(':reload');

        return $v;
    }
}
