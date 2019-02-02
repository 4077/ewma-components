<?php namespace ewma\components\ui\controllers\main;

class Components extends \Controller
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

        if ($cat = \ewma\components\models\Cat::find($selectedCatId)) {
            $components = $cat->components()->orderBy('position')->get();

            $componentsIds = table_ids($components);

            $selectedComponentId = $this->s('~:selected_component_id_by_cat_id/' . $cat->id);

            if (count($components) && !in_array($selectedComponentId, $componentsIds)) {
                $selectedComponentId = $componentsIds[0];

                $this->s('~:selected_component_id_by_cat_id/' . $cat->id, $selectedComponentId, RR);
            }

            foreach ($components as $component) {
                $componentXPack = xpack_model($component);

                $selector = $this->_selector(". .component[component_id='" . $component->id . "']");

                $v->assign('component', [
                    'ID'               => $component->id,
                    'SELECTED_CLASS'   => $component->id == $selectedComponentId ? 'selected' : '',
                    'NAME'             => $this->c('\std\ui txt:view', [
                        'path'                => '>xhr:rename',
                        'data'                => [
                            'component' => $componentXPack
                        ],
                        'class'               => 'txt',
                        'fitInputToClosest'   => '.component',
                        'placeholder'         => '...',
                        'editTriggerSelector' => $selector . " .rename.button",
                        'content'             => $component->name
                    ]),
                    'RENAME_BUTTON'    => $this->c('\std\ui tag:view', [
                        'attrs'   => [
                            'class' => 'rename button',
                            'hover' => 'hover',
                            'title' => 'Переименовать'
                        ],
                        'content' => '<div class="icon"></div>'
                    ]),
                    'DUPLICATE_BUTTON' => $this->c('\std\ui button:view', [
                        'path'    => '>xhr:duplicate',
                        'data'    => [
                            'component' => $componentXPack
                        ],
                        'class'   => 'duplicate button',
                        'title'   => 'Дублировать',
                        'content' => '<div class="icon"></div>'
                    ]),
                    'DELETE_BUTTON'    => $this->c('\std\ui button:view', [
                        'path'    => '>xhr:delete',
                        'data'    => [
                            'component' => $componentXPack
                        ],
                        'class'   => 'button delete',
                        'title'   => 'Удалить',
                        'content' => '<div class="icon"></div>'
                    ])
                ]);

                $this->c('\std\ui button:bind', [
                    'selector' => $selector,
                    'path'     => '>xhr:select',
                    'data'     => [
                        'component' => $componentXPack
                    ]
                ]);
            }

            $this->c('\std\ui sortable:bind', [
                'selector'       => $this->_selector(),
                'items_id_attr'  => 'component_id',
                'path'           => '>xhr:arrange',
                'plugin_options' => [
                    'distance' => 20
                ]
            ]);

            $v->assign([
                           'CREATE_BUTTON' => $this->c('\std\ui button:view', [
                               'path'    => '>xhr:create',
                               'data'    => [
                                   'cat' => xpack_model($cat)
                               ],
                               'class'   => 'create_button',
                               'content' => 'Создать'
                           ])
                       ]);

            $this->e('ewma/components/cats/create')->rebind(':reload');
            $this->e('ewma/components/cats/delete')->rebind(':reload');

            $this->e('ewma/components/cat_select')->rebind(':reload');
            $this->e('ewma/components/select')->rebind(':reload');

            $this->e('ewma/components/create')->rebind(':reload');
            $this->e('ewma/components/delete')->rebind(':reload');

            $this->e('ewma/components/update/cat')->rebind(':reload');
        }

        $this->css(':\css\std~, \js\jquery\ui icons');

        return $v;
    }
}
