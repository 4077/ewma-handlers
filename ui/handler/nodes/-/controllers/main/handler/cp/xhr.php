<?php namespace ewma\handlers\ui\handler\nodes\controllers\main\handler\cp;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function toggleEnabled()
    {
        if ($node = $this->unxpackModel('node')) {
            $node->enabled = !$node->enabled;
            $node->save();

            $this->e('ewma/handlers/nodes/update', ['type' => 'handler'])->trigger(['node' => $node]);
        }
    }

    public function toggleRequired()
    {
        if ($node = $this->unxpackModel('node')) {
            $node->required = !$node->required;
            $node->save();

            $this->e('ewma/handlers/nodes/update', ['type' => 'handler'])->trigger(['node' => $node]);
        }
    }

    public function createDataModifier()
    {
        if ($node = $this->unxpackModel('node')) {
            handlers()->nodes->createDataModifier($node);

            $this->e('ewma/handlers/nodes/update', ['type' => 'handler'])->trigger(['node' => $node]);
        }
    }

    public function createInput()
    {
        if ($node = $this->unxpackModel('node')) {
            handlers()->nodes->createInput($node);

            $this->e('ewma/handlers/nodes/update', ['type' => 'handler'])->trigger(['node' => $node]);
        }
    }

    public function copy()
    {
        $this->c('~app:copy', [], 'node');
    }

    public function paste()
    {
        $this->c('~app:paste', [], 'node');
    }

    public function delete()
    {
        $this->c('~app:delete', [], 'node');
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
                    'import_call' => $this->_abs('~app:import', ['node' => pack_model($node)]),
                    'export_call' => $this->_abs('~app:export', ['node' => pack_model($node)])
                ],
                'pluginOptions/title' => 'handler ' . $node->id
            ]);

            $this->c('~app:closeCp');
        }
    }
}
