<?php namespace ewma\handlers\ui\handler\nodes\controllers\main\handler;

class Cp extends \Controller
{
    public function view()
    {
        $v = $this->v();

        $node = $this->data('node');

        $copiedNodeData = $this->s('~:clipboard/data');
        $copiedNodeType = $this->s('~:clipboard/type');

        $requestData = ['node' => xpack_model($node)];

        $v->assign([
                       'ENABLED_TOGGLE_BUTTON'  => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:toggleEnabled',
                           'data'    => $requestData,
                           'class'   => 'button',
                           'title'   => $node->enabled ? 'Выключить' : 'Включить',
                           'content' => $node->enabled ? 'E-' : 'E+'
                       ]),
                       'REQUIRED_TOGGLE_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:toggleRequired',
                           'data'    => $requestData,
                           'class'   => 'button',
                           'content' => $node->required ? 'R-' : 'R+',
                           'title'   => 'Контейнер будет пропущен если результат окажется пустым'
                       ]),
                       'CREATE_DATA_MODIFIER_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:createDataModifier',
                           'data'    => $requestData,
                           'class'   => 'button',
                           'title'   => 'Создать модификатор данных обработчика',
                           'content' => '+M'
                       ]),
                       'CREATE_INPUT_BUTTON'    => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:createInput',
                           'data'    => $requestData,
                           'class'   => 'button',
                           'title'   => 'Создать вход',
                           'content' => '+I'
                       ]),
                       'EXCHANGE_BUTTON'        => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:exchange',
                           'data'    => $requestData,
                           'class'   => 'button',
                           'content' => 'Импорт/экспорт'
                       ]),
                       'COPY_BUTTON'            => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:copy',
                           'data'    => $requestData,
                           'class'   => 'button',
                           'content' => 'Копировать'
                       ]),
                       'PASTE_BUTTON'           => $this->c('\std\ui button:view', [
                           'visible' => $copiedNodeData && handlers()->nodes->canBeAssigned($copiedNodeType, $node->type),
                           'path'    => '>xhr:paste',
                           'data'    => $requestData,
                           'class'   => 'button',
                           'content' => 'Вставить'
                       ]),
                       'DELETE_BUTTON'          => $this->c('\std\ui button:view', [
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
