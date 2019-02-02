<?php namespace ewma\components\selector\controllers\main;

class Components extends \Controller
{
    public function reload()
    {
        $this->jquery()->replace($this->view());
    }

    public function view()
    {
        $v = $this->v();
        $s = $this->s('~|');

        $availableCatsIds = $this->s('~:available_cats_ids|');

        $selectedCatId = $s['selected_cat_id'];

        if (in_array($selectedCatId, $availableCatsIds)) {
            $selectedComponentId = $s['selected_component_id'];

            if ($cat = \ewma\components\models\Cat::find($selectedCatId)) {
                $components = $cat->components()->orderBy('position')->get();

                foreach ($components as $component) {
                    $v->assign('component', [
                        'SELECT_BUTTON' => $this->c('\std\ui button:view', [
                            'path'    => '>xhr:select|',
                            'data'    => [
                                'component' => xpack_model($component)
                            ],
                            'class'   => 'select_button ' . ($selectedComponentId == $component->id ? 'selected' : ''),
                            'content' => $component->name
                        ])
                    ]);
                }
            }
        }

        $this->css(':\css\std~, \js\jquery\ui icons');

        return $v;
    }
}
