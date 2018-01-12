<?php namespace ewma\handlers\ui\handler\nodes\controllers\main\value;

class Cp extends \Controller
{
    public function view()
    {
        $v = $this->v();

        $node = $this->data('node');

        $requestData = ['node' => xpack_model($node)];

        foreach (handlers()->valuesTypes as $valueType => $valueTypeHint) {
            $v->assign('type', [
                'SET_BUTTON' => $this->c('\std\ui button:view', [
                    'path'    => '>xhr:setValueType:' . $valueType,
                    'data'    => $requestData,
                    'class'   => 'button',
                    'title'   => $valueTypeHint,
                    'content' => $valueType
                ]),
            ]);
        }

        $v->assign([
                       'ENABLED_TOGGLE_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:toggleEnabled',
                           'data'    => $requestData,
                           'class'   => 'button',
                           'title'   => $node->enabled ? 'Выключить' : 'Включить',
                           'content' => $node->enabled ? 'E-' : 'E+'
                       ]),
                       'EXCHANGE_BUTTON'       => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:exchange',
                           'data'    => $requestData,
                           'class'   => 'button',
                           'content' => 'Импорт/экспорт'
                       ]),
                       'COPY_BUTTON'           => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:copy',
                           'data'    => $requestData,
                           'class'   => 'button',
                           'content' => 'Копировать'
                       ]),
                       'DELETE_BUTTON'         => $this->c('\std\ui button:view', [
                           'ctrl'    => [
                               'path' => '>xhr:delete',
                               'data' => $requestData
                           ],
                           'class'   => 'button',
                           'content' => 'Удалить'
                       ])
                   ]);

        $this->css();

        return $v;
    }
}
