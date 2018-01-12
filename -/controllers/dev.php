<?php namespace ewma\handlers\controllers;

class Dev extends \Controller
{
    public function test()
    {
        return handlers()->render('test:content');
    }

    public function j()
    {
        return p($this->data, true);
    }


    public function k()
    {
        return $this->data;
    }

    public function examples()
    {
        handlers()->render(546, [
            'x' => 24,
            'y' => 144
        ]);

        $this->c('\ewma\handlers~:render', [
            'source' => 546,
            'data'   => [
                'x' => 24,
                'y' => 144
            ]
        ]);

        handlers()->render('dep/post-update:dev', [
            'reset' => [
                'ui'            => true,
                'sessionEvents' => false
            ]
        ]);
    }
}
