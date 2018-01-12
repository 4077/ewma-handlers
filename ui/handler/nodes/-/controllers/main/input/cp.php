<?php namespace ewma\handlers\ui\handler\nodes\controllers\main\input;

class Cp extends \Controller
{
    public function view()
    {
        $v = $this->v();

        $node = $this->data('node');

        $copiedNodeData = $this->s('~:clipboard/data');
        $copiedNodeType = $this->s('~:clipboard/type');

        $requestData = ['node' => xpack_model($node)];

        foreach (handlers()->containersCombineModes as $combineMode => $combineModeHint) {
            $v->assign('combine_mode', [
                'SET_BUTTON' => $this->c('\std\ui button:view', [
                    'path'    => '>xhr:setCombineMode:' . $combineMode,
                    'data'    => $requestData,
                    'class'   => 'button',
                    'title'   => $combineModeHint,
                    'content' => $combineMode
                ])
            ]);
        }

        $v->assign([
                       'TYPE_CLASS'                  => strtolower($node->type),
                       'ENABLED_TOGGLE_BUTTON'       => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:toggleEnabled',
                           'data'    => $requestData,
                           'class'   => 'button',
                           'title'   => $node->enabled ? 'Выключить' : 'Включить',
                           'content' => $node->enabled ? 'E-' : 'E+'
                       ]),
                       'REQUIRED_TOGGLE_BUTTON'      => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:toggleRequired',
                           'data'    => $requestData,
                           'class'   => 'button',
                           'content' => $node->required ? 'R-' : 'R+',
                           'title'   => 'Вызов будет пропущен если результат окажется пустым'
                       ]),
                       'CREATE_CALL_BUTTON'          => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:createCall',
                           'data'    => $requestData,
                           'class'   => 'button',
                           'title'   => 'Создать вызов',
                           'content' => '+C'
                       ]),
                       'CREATE_VALUE_BUTTON'         => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:createValue',
                           'data'    => $requestData,
                           'class'   => 'button',
                           'title'   => 'Создать значение',
                           'content' => '+V'
                       ]),
                       'CREATE_HANDLER_BUTTON'       => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:createHandler',
                           'data'    => $requestData,
                           'class'   => 'button',
                           'title'   => 'Добавить обработчик',
                           'content' => '+H'
                       ]),
                       'CREATE_DATA_MODIFIER_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:createDataModifier',
                           'data'    => $requestData,
                           'class'   => 'button',
                           'title'   => 'Создать модификатор данных обработчика',
                           'content' => '+M'
                       ]),
                       'EXCHANGE_BUTTON'             => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:exchange',
                           'data'    => $requestData,
                           'class'   => 'button',
                           'content' => 'Импорт/экспорт'
                       ]),
                       'COPY_BUTTON'                 => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:copy',
                           'data'    => $requestData,
                           'class'   => 'button',
                           'content' => 'Копировать'
                       ]),
                       'PASTE_BUTTON'                => $this->c('\std\ui button:view', [
                           'visible' => $copiedNodeData && handlers()->nodes->canBeAssigned($copiedNodeType, $node->type),
                           'path'    => '>xhr:paste',
                           'data'    => $requestData,
                           'class'   => 'button',
                           'content' => 'Вставить'
                       ]),
                       'DELETE_BUTTON'               => $this->c('\std\ui button:view', [
                           'ctrl'    => [
                               'path' => '>xhr:delete',
                               'data' => $requestData,
                           ],
                           'class'   => 'button',
                           'content' => 'Удалить'
                       ])
                   ]);

        $this->css();

        return $v;
    }
}
