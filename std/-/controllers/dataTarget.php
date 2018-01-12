<?php namespace ewma\handlers\std\controllers;

class DataTarget extends \Controller
{
    public function set()
    {
        $target = $this->data('target');

        if (is_array($target)) {
            $type = $target['type'];

            if ($type == 'storage') {
                $this->d($target['path'], $this->data('value'), RR);
            }
        }
    }

    public function get()
    {
        $target = $this->data('target');

        if (is_array($target)) {
            $type = $target['type'];

            if ($type == 'storage') {
                return $this->d($target['path']);
            }
        }
    }


    public function pack()
    {
        $target = $this->data('target');

        if (is_array($target)) {
            $type = $target['type'];

            if ($type == 'model' || $type == 'field' || $type == 'cell') {
                $target[$type] = ('pack_' . $type)($target['model']);
            }

            return j64_($target);
        }
    }

    public function unpack()
    {

    }
}
