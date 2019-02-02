<?php namespace ewma\handlers\ui\controllers\main;

class Handlers extends \Controller
{
    public function reload()
    {
        $this->jquery()->replace($this->view());
    }

    public function view()
    {
        $v = $this->v();

        if ($cat = \ewma\handlers\models\Cat::find($this->s('~:selected_cat_id'))) {
            $handlers = $cat->handlers()->orderBy('position')->get();

            $handlersIds = table_ids($handlers);

            $selectedHandlerId = $this->s('~:selected_handler_id_by_cat_id/' . $cat->id);

            if (count($handlers) && !in_array($selectedHandlerId, $handlersIds)) {
                $selectedHandlerId = $handlersIds[0];

                $this->s('~:selected_handler_id_by_cat_id/' . $cat->id, $selectedHandlerId, RR);
            }

            foreach ($handlers as $handler) {
                $handlerXPack = xpack_model($handler);

                $selector = $this->_selector(". .handler[handler_id='" . $handler->id . "']");

                $v->assign('handler', [
                    'ID'               => $handler->id,
                    'SELECTED_CLASS'   => $handler->id == $selectedHandlerId ? 'selected' : '',
                    'NAME'             => $this->c('\std\ui txt:view', [
                        'path'                => '>xhr:rename',
                        'data'                => [
                            'handler' => $handlerXPack
                        ],
                        'class'               => 'txt',
                        'fitInputToClosest'   => '.handler',
                        'placeholder'         => '...',
                        'editTriggerSelector' => $selector . " .rename.button",
                        'content'             => $handler->name
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
                            'handler' => $handlerXPack
                        ],
                        'class'   => 'duplicate button',
                        'title'   => 'Дублировать',
                        'content' => '<div class="icon"></div>'
                    ]),
                    'DELETE_BUTTON'    => $this->c('\std\ui button:view', [
                        'path'    => '>xhr:delete',
                        'data'    => [
                            'handler' => $handlerXPack
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
                        'handler' => $handlerXPack
                    ]
                ]);
            }

            $this->c('\std\ui sortable:bind', [
                'selector'       => $this->_selector(),
                'items_id_attr'  => 'handler_id',
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

            $this->e('ewma/handlers/cats/create')->rebind(':reload');
            $this->e('ewma/handlers/cats/delete')->rebind(':reload');

            $this->e('ewma/handlers/cat_select')->rebind(':reload');
            $this->e('ewma/handlers/select')->rebind(':reload');

            $this->e('ewma/handlers/create')->rebind(':reload');
            $this->e('ewma/handlers/delete')->rebind(':reload');

            $this->e('ewma/handlers/update/cat')->rebind(':reload');
        }

        $this->css(':\css\std~, \js\jquery\ui icons');

        return $v;
    }
}
