<?php namespace ewma\handlers\ui\controllers;

class Main extends \Controller
{
    public function __create()
    {
        $this->a() or $this->lock();
    }

    public function reload()
    {
        $this->jquery()->replace($this->view());
    }

    public function view()
    {
        $v = $this->v();

        $s = $this->s(false, [
            'selected_cat_id'               => false,
            'selected_handler_id_by_cat_id' => [],
            'cats_width'                    => 250
        ]);

        $v->assign([
                       'CATS'       => $this->c('>cats:view'),
                       'CATS_WIDTH' => $s['cats_width'],
                       'HANDLERS'   => $this->c('>handlers:view'),
                       'HANDLER'    => $this->c('>handler:view')
                   ]);

        $this->c('\std\ui resizable:bind', [
            'selector'      => $this->_selector('|') . ' .cats',
            'path'          => '>xhr:updateCatsWidth',
            'pluginOptions' => [
                'handles' => 'e'
            ]
        ]);

        $this->c('\std\ui\dialogs~:addContainer:ewma/handlers');

        $this->css();

        $this->app->html->setFavicon(abs_url('-/ewma/favicons/dev_handlers.png'));

        return $v;
    }
}
