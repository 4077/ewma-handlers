<?php namespace ewma\handlers\ui\handler\nodes\controllers\main\input;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function updatePath()
    {
        if ($node = $this->unxpackModel('node')) {
            $txt = \std\ui\Txt::value($this);

            handlers()->nodes->updateData($node, 'path', $txt->value);

            $txt->response();
        }
    }
}
