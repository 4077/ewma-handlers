<?php namespace ewma\handlers\ui\controllers\main\handler;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function compile()
    {
        if ($handler = $this->unpackModel('handler')) {
            handlers()->compile($handler);
        }
    }
}
