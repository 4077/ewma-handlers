<?php namespace ewma\handlers\ui\handler\controllers\main;

class App extends \Controller
{
    public function export()
    {
        if ($node = $this->unpackModel('node')) {
            return handlers()->nodes->export($node);
        }
    }

    public function import()
    {
        if ($node = $this->unpackModel('node')) {
            handlers()->nodes->import($node, $this->data('data'), $this->data('skip_first_level'));

            $this->e('ewma/handlers/nodes/update')->trigger(['node' => $node]);
        }
    }

    public function setCat()
    {
        $cat = \ewma\handlers\models\Cat::find($this->data('cat_id'));
        $handler = \ewma\handlers\models\Handler::find($this->data('handler_id'));

        if ($cat && $handler) {
            $catIdBefore = $handler->target_id;

            $handler->cat()->associate($cat);
            $handler->save();

            $this->e('ewma/handlers/update/cat', [
                'handler_id' => $handler->id,
                'cat_id'     => $catIdBefore
            ])->trigger(['handler' => $handler]);
        }
    }
}
