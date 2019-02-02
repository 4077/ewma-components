<?php namespace ewma\components\ui\controllers\main\components;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function __create()
    {
        $this->a() or $this->lock();
    }

    public function select()
    {
        if ($component = $this->unpackModel('component')) {
            $this->s('~:selected_component_id_by_cat_id/' . $component->cat_id, $component->id, RA);

            $this->e('ewma/components/select')->trigger();
        }
    }

    public function create()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $newComponent = components()->create($cat);

            $this->s('~:selected_component_id_by_cat_id/' . $cat->id, $newComponent->id, RA);

            $this->e('ewma/components/create')->trigger();
        }
    }

    public function duplicate()
    {
        if ($component = $this->unxpackModel('component')) {
            $newComponent = components()->duplicate($component);

            $this->s('~:selected_component_id_by_cat_id/' . $this->s('~:selected_cat_id'), $newComponent->id, RA);

            $this->e('ewma/components/create')->trigger();
        }
    }

    public function delete()
    {
        if ($this->data('discarded')) {
            $this->c('\std\ui\dialogs~:close:deleteConfirm|ewma/components');
        } else {
            if ($component = $this->unxpackModel('component')) {
                if ($this->data('confirmed')) {
                    components()->delete($component);

                    $this->e('ewma/components/delete', ['component_id' => $component->id])->trigger();

                    $this->c('\std\ui\dialogs~:close:deleteConfirm|ewma/components');
                } else {
                    $this->c('\std\ui\dialogs~:open:deleteConfirm|ewma/components', [
                        'path'          => '@deleteConfirm:view',
                        'data'          => [
                            'component'    => pack_model($component),
                            'confirm_call' => $this->_abs(':delete|', ['component' => $this->data['component']]),
                            'discard_call' => $this->_abs(':delete|', ['component' => $this->data['component']])
                        ],
                        'pluginOptions' => [
                            'resizable' => 'false'
                        ]
                    ]);
                }
            }
        }
    }

    public function arrange()
    {
        foreach ((array)$this->data('sequence') as $n => $id) {
            if ($component = \ewma\components\models\Component::find($id)) {
                $component->position = (int)$n * 10;
                $component->save();
            }
        }
    }

    public function rename()
    {
        if ($component = $this->unpackModel('component')) {
            $txt = \std\ui\Txt::value($this);

            $component->name = $txt->value;
            $component->save();

            $txt->response();

            $this->e('ewma/components/update/name', ['component_id' => $component->id])->trigger(['component' => $component]);
        }
    }
}
