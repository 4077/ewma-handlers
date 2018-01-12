<?php namespace ewma\handlers\ui\handler\nodes\controllers\main;

class App extends \Controller
{
    public function bindBarsClick()
    {
        $this->widget('~:|', 'bindBarsClick');
    }

    public function closeCp()
    {
        $this->widget('~:|', 'closeCp');
    }

    public function copy()
    {
        if ($node = $this->unpackModel('node')) {
            $nodeExport = j_(handlers()->nodes->export($node));

            $this->s('~:clipboard/data', $nodeExport, RR);
            $this->s('~:clipboard/type', $node->type, RR);

            $this->e('ewma/handlers/nodes/copy')->trigger();
        }
    }

    public function paste()
    {
        if ($node = $this->unpackModel('node')) {
            if ($nodeExport = _j($this->s('~:clipboard/data'))) {
                handlers()->nodes->import($node, $nodeExport);

                $this->e('ewma/handlers/nodes/copy')->trigger();
            }
        }
    }

    public function delete()
    {
        if ($node = $this->unpackModel('node')) {
            handlers()->nodes->delete($node);

            $this->e('ewma/handlers/nodes/copy')->trigger();
        }
    }

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

    public function createHandlerDialog()
    {
        // выбор конкретного, задатчик пути
    }
}
