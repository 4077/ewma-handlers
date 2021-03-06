<?php namespace ewma\handlers\ui\handler\nodes\controllers\main\handler;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function updateSource()
    {
        if ($node = $this->unxpackModel('node')) {
            $txt = \std\ui\Txt::value($this);

            handlers()->nodes->setHandlerSource($node, $txt->value);

            $txt->response();
        }
    }

    public function updatePath()
    {
        if ($node = $this->unxpackModel('node')) {
            $txt = \std\ui\Txt::value($this);

            handlers()->nodes->updateData($node, 'path', $txt->value);

            $txt->response();
        }
    }

    public function updateMappings()
    {
        if ($node = $this->unxpackModel('node')) {
            $txt = \std\ui\Txt::value($this);

            handlers()->nodes->updateData($node, 'mappings', $txt->value);

            $txt->response();
        }
    }

    public function arrange()
    {
        if ($this->dataHas('sequence array')) {
            foreach ($this->data['sequence'] as $n => $nodeId) {
                if (is_numeric($n) && $node = \ewma\handlers\models\Node::find($nodeId)) {
                    $node->update(['position' => ($n + 1) * 10]);
                }
            }
        }
    }
}
