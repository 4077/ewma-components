<?php namespace ewma\components\ui\component\controllers;

class Main extends \Controller
{
    private $component;

    public function __create()
    {
        if ($component = $this->unpackModel('component')) {
            $this->component = $component;

            $this->instance_($this->component->id);
        }
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $component = $this->component;

        $handlers = components()->getHandlers($component);

        foreach ($handlers as $handler) {
            $v->assign('instance', [
                'NAME'          => $handler->instance ?: '...',
                'HANDLER'       => $this->c('\ewma\handlers\ui\handler~:view', [
                    'handler' => $handler
                ]),
                'DELETE_BUTTON' => $this->c('\std\ui button:view', [
                    'path'  => '>xhr:delete',
                    'data'  => [
                        'handler' => xpack_model($handler)
                    ],
                    'class' => 'delete button',
                    'icon'  => 'fa fa-trash-o'
                ])
            ]);
        }

        $v->assign([
                       'CREATE_INSTANCE_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:create',
                           'data'    => [
                               'component' => xpack_model($component)
                           ],
                           'class'   => 'create_button',
                           'content' => 'Создать'
                       ])
                   ]);

        $this->css();

        $this->e('components/component/handlers/create')->rebind(':reload');
        $this->e('components/component/handlers/delete')->rebind(':reload');

        return $v;
    }
}
