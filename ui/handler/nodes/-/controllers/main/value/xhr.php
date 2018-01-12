<?php namespace ewma\handlers\ui\handler\nodes\controllers\main\value;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function updateStringValue()
    {
        if ($node = $this->unxpackModel('node')) {
            $txt = \std\ui\Txt::value($this);

            handlers()->nodes->updateData($node, 'value/string', $txt->value);

            $txt->response();
        }
    }

    public function invertBoolValue()
    {
        if ($node = $this->unxpackModel('node')) {
            $value = handlers()->nodes->getData($node, 'value/bool');

            handlers()->nodes->updateData($node, 'value/bool', !$value);

            $this->e('ewma/handlers/nodes/update', ['type' => 'value'])->trigger(['node' => $node]);
        }
    }
}
