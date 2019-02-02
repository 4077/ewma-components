<?php namespace ewma\components\ui\component\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function createInstance()
    {
        if ($component = $this->unxpackModel('component')) {
            components()->createHandler($component, $this->data('instance') ?? '');
        }
    }

    public function create()
    {
        if ($component = $this->unxpackModel('component')) {
            $this->c('\std\ui\dialogs~:open:createInstanceForm, ss|ewma/components', [
                'path'          => '~createInstanceForm:view',
                'data'          => [
                    'component' => pack_model($component)
                ],
                'pluginOptions' => [
                    'title' => 'Создать обработчик для ' . components()->getFullName($component)
                ]
            ]);
        }
    }

    public function delete()
    {
        if ($this->data('discarded')) {
            $this->c('\std\ui\dialogs~:close:deleteConfirm|ewma/components');
        } else {
            if ($handler = $this->unxpackModel('handler')) {
                if ($this->data('confirmed')) {
                    handlers()->delete($handler);

                    if ($component = $handler->target) {
                        $this->e('components/component/handlers/delete')->trigger(['component' => $component]);
                    }

                    $this->c('\std\ui\dialogs~:close:deleteConfirm|ewma/components');
                } else {
                    if ($component = $handler->target) {
                        $handlerName = components()->getFullName($component) . '|' . $handler->instance;
                    } else {
                        $handlerName = '...|' . $handler->instance;
                    }

                    $this->c('\std\ui\dialogs~:open:deleteConfirm|ewma/components', [
                        'path'            => '\std dialogs/confirm~:view',
                        'data'            => [
                            'confirm_call' => $this->_abs(':delete', ['handler' => $this->data['handler']]),
                            'discard_call' => $this->_abs(':delete', ['handler' => $this->data['handler']]),
                            'message'      => 'Удалить обработчик <b>' . $handlerName . '</b>?'
                        ],
                        'forgot_on_close' => true,
                        'pluginOptions'   => [
                            'resizable' => 'false'
                        ]
                    ]);
                }
            }
        }
    }
}
