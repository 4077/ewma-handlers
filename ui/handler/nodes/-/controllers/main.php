<?php namespace ewma\handlers\ui\handler\nodes\controllers;

class Main extends \Controller
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
    }

    public function view()
    {
        $v = $this->v('|');

        $node = $this->node;

        $v->assign([
                       'NODE_ID' => $node->id
                   ]);

        $nestedNodes = $node->nested()->orderBy('position')->get();

        foreach ($nestedNodes as $nestedNode) {
            $v->assign('node', [
                'ID'      => $nestedNode->id,
                'CONTENT' => $this->nodeContentView($nestedNode)
            ]);
        }

        if (count($nestedNodes) > 1) {
            $this->c('\std\ui sortable:bind', [
                'selector'       => $this->_selector('|') . " > .nodes",
                'path'           => '>xhr:arrange',
                'items_id_attr'  => 'node_' . $node->id . '_node_id',
                'data'           => [
                    'node' => xpack_model($node)
                ],
                'plugin_options' => [
                    'axis'     => 'y',
                    'distance' => 15
                ]
            ]);
        }

        $this->css();

        $this->widget(':|'); // todo html addCall

//        $this->e('ewma/handlers/nodes/update')->rebind(':reload');

        return $v;
    }

    private function nodeContentView($node)
    {
        $nodeViewData = ['node' => $node];

        if ($node->type == 'HANDLER') {
            return $this->c('>handler:view', $nodeViewData);
        }

        if ($node->type == 'CALL') {
            return $this->c('>call:view', $nodeViewData);
        }

        if ($node->type == 'VALUE') {
            return $this->c('>value:view', $nodeViewData);
        }

        if ($node->type == 'DATA_MODIFIER') {
            return $this->c('>dataModifier:view', $nodeViewData);
        }
    }
}
