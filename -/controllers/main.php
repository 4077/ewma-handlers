<?php namespace ewma\handlers\controllers;

class Main extends \Controller
{
    public function compile()
    {
        handlers()->compileAll();
    }

    public function compileHandler()
    {
        if ($handler = \ewma\handlers\models\Handler::find($this->data('handler_id'))) {
            return handlers()->compile($handler);
        }
    }

    public function render()
    {
        return handlers()->render($this->data('source'), $this->data('data'));
    }
}
