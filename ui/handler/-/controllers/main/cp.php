<?php namespace ewma\handlers\ui\handler\controllers\main;

class Cp extends \Controller
{
    public function view()
    {
        $v = $this->v();

        $node = $this->data('node');

        $copiedNodeData = $this->s('nodes~:clipboard/data');
        $copiedNodeType = $this->s('nodes~:clipboard/type');

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
                       'EXCHANGE_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:exchange',
                           'data'    => $requestData,
                           'class'   => 'button',
                           'content' => 'Импорт/экспорт'
                       ]),
                       'PASTE_BUTTON'    => $this->c('\std\ui button:view', [
                           'visible' => $copiedNodeData && handlers()->nodes->canBeAssigned($copiedNodeType, $node->type),
                           'path'    => '>xhr:paste',
                           'data'    => $requestData,
                           'class'   => 'button',
                           'content' => 'Вставить'
                       ]),
                       'DELETE_BUTTON'   => $this->c('\std\ui button:view', [
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
