<?php namespace ewma\handlers\ui\handler\nodes\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function arrange()
    {
        foreach ((array)$this->data('sequence') as $n => $id) {
            if ($node = \ewma\handlers\models\Node::find($id)) {
                $node->position = (int)$n * 10;
                $node->save();
            }
        }
    }
}
