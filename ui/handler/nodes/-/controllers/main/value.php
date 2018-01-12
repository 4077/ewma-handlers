<?php namespace ewma\handlers\ui\handler\nodes\controllers\main;

class Value extends \Controller
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

        $valueData = _j($node->data);

        $v->assign([
                       'NODE_ID'        => $node->id,
                       'DISABLED_CLASS' => $node->enabled ? '' : 'disabled',
                       'VALUE'          => $this->valueView($valueData),
                       'CP'             => $this->c('>cp:view', ['node' => $node])
                   ]);

        $this->css();

        $this->e('ewma/handlers/nodes/update', ['type' => 'value'])->rebind(':reload');

        return $v;
    }

    private function valueView($nodeData)
    {
        $node = $this->node;

        $requestData = ['node' => xpack_model($node)];

        if ($nodeData['type'] == 'bool') {
            return $this->c('\std\ui button:view', [
                'path'    => '>xhr:invertBoolValue',
                'data'    => $requestData,
                'class'   => 'bool ' . ($nodeData['value']['bool'] ? 'true' : 'false'),
                'title'   => 'Двоичное значение',
                'content' => $nodeData['value']['bool'] ? 'true' : 'false'
            ]);
        }

        if ($nodeData['type'] == 'string') {
            return $this->c('\std\ui txt:view', [
                'path'              => '>xhr:updateStringValue',
                'title'             => 'Строковое значение',
                'data'              => $requestData,
                'class'             => 'txt string',
                'fitInputToClosest' => '.container',
                'content'           => $nodeData['value']['string']
            ]);
        }

        if ($nodeData['type'] == 'array') {
            return $this->c('\std\ui\data~:view|' . $this->_nodeId() . '/' . $node->id, [
                'read_call'  => [':readArrayTypeValue', $requestData],
                'write_call' => [':writeArrayTypeValue', $requestData]
            ]);
        }
    }

    public function readArrayTypeValue()
    {
        $nodeData = _j($this->node->data);

        return $nodeData['value'][$nodeData['type']];
    }

    public function writeArrayTypeValue()
    {
        handlers()->nodes->updateData($this->node, 'value/array', $this->data['data']);
    }
}
