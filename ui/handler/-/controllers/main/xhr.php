<?php namespace ewma\handlers\ui\handler\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function createHandler()
    {
        if ($node = $this->unxpackModel('node')) {
            handlers()->nodes->createHandler($node);

            $this->e('ewma/handlers/nodes/update')->trigger(['node' => $node]);
        }
    }

    public function createCall()
    {
        if ($node = $this->unxpackModel('node')) {
            handlers()->nodes->createCall($node);

            $this->e('ewma/handlers/nodes/update')->trigger(['node' => $node]);
        }
    }

    public function createValue()
    {
        if ($node = $this->unxpackModel('node')) {
            handlers()->nodes->createValue($node);

            $this->e('ewma/handlers/nodes/update')->trigger(['node' => $node]);
        }
    }

    public function createDataModifier()
    {
        if ($node = $this->unxpackModel('node')) {
            handlers()->nodes->createDataModifier($node);

            $this->e('ewma/handlers/nodes/update')->trigger(['node' => $node]);
        }
    }

    public function reload()
    {
        $this->c('~:reload', [], 'handler');
    }
}
