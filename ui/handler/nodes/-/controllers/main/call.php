<?php namespace ewma\handlers\ui\handler\nodes\controllers\main;

class Call extends \Controller
{
    private $node;

    public function __create()
    {
        if ($node = $this->unpackModel('node')) {
            $this->node = $node;

            $this->instance_($this->node->id);
        } else {
            $this->lock();
        }
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());

        $this->c('~app:bindBarsClick');
    }

    public function view()
    {
        $v = $this->v('|');

        $node = $this->node;

        $requestsData = ['node' => xpack_model($node)];

        $nodeData = _j($node->data);

        $v->assign([
                       'NODE_ID'        => $node->id,
                       'DISABLED_CLASS' => $node->enabled ? '' : 'disabled',
                       'PATH_TXT'       => $this->c('\std\ui txt:view', [
                           'path'              => '>xhr:updatePath',
                           'data'              => $requestsData,
                           'class'             => 'txt',
                           'title'             => 'Путь к методу',
                           'emptyContent'      => '...',
                           'fitInputToClosest' => '.container',
                           'content'           => $nodeData['path']
                       ]),
                       'CP'             => $this->c('>cp:view', ['node' => $node])
                   ]);

        if ($node['required']) {
            $v->assign('required');
        }

        $nestedNodes = $node->nested()->orderBy('position')->get();

        foreach ($nestedNodes as $nestedNode) {
            if ($nestedNode->type == 'INPUT') {
                $v->assign('node', [
                    'ID'      => $nestedNode->id,
                    'CONTENT' => $this->c('@input:view', [
                        'node' => $nestedNode
                    ])
                ]);
            }

            if ($nestedNode->type == 'DATA_MODIFIER') {
                $v->assign('node', [
                    'ID'      => $nestedNode->id,
                    'CONTENT' => $this->c('@dataModifier:view', [
                        'node' => $nestedNode
                    ])
                ]);
            }
        }

        if (count($nestedNodes) > 1) {
            $this->c('\std\ui sortable:bind', [
                'selector'       => $this->_selector("|" . $node->id) . " > .nodes",
                'path'           => '>xhr:arrange',
                'items_id_attr'  => 'node_' . $node->id . '_node_id',
                'data'           => $requestsData,
                'plugin_options' => [
                    'axis'     => 'y',
                    'distance' => 15
                ]
            ]);
        }

        $this->css(':\js\jquery\ui icons');

        $this->e('ewma/handlers/nodes/update', ['type' => 'call'])->rebind(':reload');

        return $v;
    }
}
