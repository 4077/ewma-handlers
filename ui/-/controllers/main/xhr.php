<?php namespace ewma\handlers\ui\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function updateCatsWidth()
    {
        $s = &$this->s('~');

        $s['cats_width'] = $this->data('width');
    }
}
