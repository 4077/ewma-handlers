<?php namespace ewma\handlers\ui\handler\controllers;

class Main extends \Controller
{
    private $handler;

    public function __create()
    {
        $handler = $this->unpackModel('handler') or (
            $node = $this->unpackModel('node') and $handler = $node->handler
        );

        if ($handler) {
            $this->handler = $handler;

            $this->instance_($this->handler->id);
        }
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $node = handlers()->getRootNode($this->handler);

        $nodeData = _j($node->data);

        $requestData = ['node' => xpack_model($node)];

        $v->assign([
                       'NODE_ID'                     => $node->id,
                       'CREATE_CALL_BUTTON'          => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:createCall',
                           'data'    => $requestData,
                           'class'   => 'button',
                           'title'   => 'Создать вызов',
                           'content' => '+C'
                       ]),
                       'CREATE_HANDLER_BUTTON'       => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:createHandler',
                           'data'    => $requestData,
                           'class'   => 'button',
                           'title'   => 'Добавить обработчик',
                           'content' => '+H'
                       ]),
                       'CREATE_VALUE_BUTTON'         => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:createValue',
                           'data'    => $requestData,
                           'class'   => 'button',
                           'title'   => 'Создать значение',
                           'content' => '+V'
                       ]),
                       'CREATE_DATA_MODIFIER_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:createDataModifier',
                           'data'    => $requestData,
                           'class'   => 'button',
                           'title'   => 'Создать модификатор данных обработчика',
                           'content' => '+M'
                       ]),
                       'CP'                          => $this->c('>cp:view', [
                           'node' => handlers()->getRootNode($this->handler)
                       ]),
                       'NODES'                       => $this->c('nodes~:view', [
                           'node' => handlers()->getRootNode($this->handler)
                       ])
                   ]);

        if ($nodeData['combine_mode']) {
            $v->assign('combine_mode', [
                'VALUE' => $nodeData['combine_mode']
            ]);
        }

        $this->css();

        $this->widget(':|', [
            'paths'   => [
                'reload' => $this->_p('>xhr:reload')
            ],
            'handler' => xpack_model($this->handler)
        ]);

        $this->e('ewma/handlers/nodes/copy')->rebind(':selfReload');
        $this->e('ewma/handlers/nodes/update')->rebind(':reload');

        return $v;
    }

    public function selfReload()
    {
        $this->jquery()->{$this->_nodeId()}('selfReload');
    }
}
