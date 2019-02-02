<?php namespace ewma\components\ui\controllers\main\cats\node;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function __create()
    {
        $this->a() or $this->lock();
    }

    public function select()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $this->s('~:selected_cat_id|', $cat->id, RR);

            $this->e('ewma/components/cat_select')->trigger();
        }
    }

    public function create()
    {
        if ($cat = $this->unpackModel('cat')) {
            $newCat = components()->cats->create($cat);

            $this->s('~:selected_cat_id|', $newCat->id, RR);

            $this->e('ewma/components/cats/create', ['cat_id' => $cat->id])->trigger(['cat' => $cat]);
        }
    }

    public function duplicate()
    {
        if ($cat = $this->unpackModel('cat')) {
            $newCat = components()->cats->duplicate($cat);

            $this->s('~:selected_cat_id', $newCat->id, RR);

            $this->e('ewma/components/cats/create', ['cat_id' => $cat->id])->trigger(['cat' => $cat]);
        }
    }

    public function delete()
    {
        if ($this->data('discarded')) {
            $this->c('\std\ui\dialogs~:close:deleteCatConfirm|ewma/components');
        } else {
            if ($cat = $this->unpackModel('cat')) {
                $catsIds = \ewma\Data\Tree::getIds($cat);
                $nestedCatsCount = count($catsIds) - 1;

                $components = \ewma\components\models\Component::whereIn('cat_id', $catsIds)->get();
                $componentsCount = count($components);

                if ($this->dataHas('confirmed') || (!$nestedCatsCount && !$componentsCount)) {
                    components()->cats->delete($cat);

                    $selectedCatId = &$this->s('~:selected_cat_id');
                    if (in_array($selectedCatId, $catsIds)) {
                        $selectedCatId = false;
                    }

                    $this->e('ewma/components/cats/delete')->trigger();

                    $this->c('\std\ui\dialogs~:close:deleteCatConfirm|ewma/components');
                } else {
                    $this->c('\std\ui\dialogs~:open:deleteCatConfirm|ewma/components', [
                        'path'          => '~cats/deleteConfirm:view',
                        'data'          => [
                            'confirm_call'      => $this->_abs(':delete|', ['cat' => $this->data['cat']]),
                            'discard_call'      => $this->_abs(':delete|', ['cat' => $this->data['cat']]),
                            'cat_name'          => $cat->name,
                            'components_count'  => $componentsCount,
                            'nested_cats_count' => $nestedCatsCount
                        ],
                        'pluginOptions' => [
                            'resizable' => false
                        ]
                    ]);
                }
            }
        }
    }

    public function compile()
    {
        if ($cat = $this->unpackModel('cat')) { // todo compile cat components
            components()->compileAll();
        }
    }

    public function rename()
    {
        if ($cat = $this->unpackModel('cat')) {
            $txt = \std\ui\Txt::value($this);

            $cat->name = $txt->value;
            $cat->save();

            $txt->response();

            $this->e('ewma/components/cats/update/name', ['cat_id' => $cat->id])->trigger(['cat' => $cat]);
        }
    }

    public function exchange()
    {
        if ($cat = $this->unpackModel('cat')) {
            $this->c('\std\ui\dialogs~:open:exchange|ewma/components', [
                'default'             => [
                    'pluginOptions/width' => 500
                ],
                'path'                => '\std\data\exchange~:view|ewma/components',
                'data'                => [
                    'target_name' => '#' . $cat->id . ' ' . $cat->path,
                    'import_call' => $this->_abs('<<app:import', ['cat' => pack_model($cat)]),
                    'export_call' => $this->_abs('<<app:export', ['cat' => pack_model($cat)])
                ],
                'pluginOptions/title' => 'components'
            ]);
        }
    }
}
