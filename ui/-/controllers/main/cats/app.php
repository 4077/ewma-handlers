<?php namespace ewma\handlers\ui\controllers\main\cats;

class App extends \Controller
{
    public function getQueryBuilder()
    {
        return \ewma\handlers\models\Cat::orderBy('position');
    }

    public function moveCallback()
    {
        $cat = $this->data['cat'];

        handlers()->updatePaths($cat);
    }

    public function sortCallback()
    {
        $cat = $this->data['cat'];

        handlers()->updatePaths($cat);
    }

    public function export()
    {
        if ($cat = $this->unpackModel('cat')) {
            return handlers()->cats->export($cat);
        }
    }

    public function import()
    {
        if ($cat = $this->unpackModel('cat')) {
            handlers()->cats->import($cat, $this->data('data'), $this->data('skip_first_level'));

            $this->e('ewma/handlers/cats/import')->trigger();
        }
    }
}
