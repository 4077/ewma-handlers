<?php namespace ewma\handlers\ui\handler\controllers\main\cp;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function setCombineMode($mode)
    {
        if ($node = $this->unxpackModel('node')) {
            handlers()->nodes->setCombineMode($node, $mode);

            $this->e('ewma/handlers/nodes/update')->trigger(['node' => $node]);
        }
    }

    public function paste()
    {
        $this->c('nodes~app:paste', [], 'node');
    }

    public function exchange()
    {
        if ($node = $this->unxpackModel('node')) {
            $this->c('\std\ui\dialogs~:open:exchange|ewma/handlers', [
                'default'             => [
                    'pluginOptions/width' => 500
                ],
                'path'                => '\std\data\exchange~:view|ewma/handlers',
                'data'                => [
                    'target_name' => '#' . $node->id,
                    'import_call' => $this->_abs('<<app:import', ['node' => pack_model($node)]),
                    'export_call' => $this->_abs('<<app:export', ['node' => pack_model($node)])
                ],
                'pluginOptions/title' => 'handlers'
            ]);
        }
    }
}
