<?php namespace ewma\handlers\ui\controllers\main\handlers;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function __create()
    {
        $this->a() or $this->lock();
    }

    public function select()
    {
        if ($handler = $this->unpackModel('handler')) {
            $this->s('~:selected_handler_id_by_cat_id/' . $handler->target_id, $handler->id, RA);

            $this->e('ewma/handlers/select')->trigger();
        }
    }

    public function create()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $newHandler = handlers()->create();

            $cat->handlers()->save($newHandler);

            $this->s('~:selected_handler_id_by_cat_id/' . $cat->id, $newHandler->id, RA);

            handlers()->updatePaths($cat);

            $this->e('ewma/handlers/create')->trigger();
        }
    }

    public function duplicate()
    {
        if ($handler = $this->unxpackModel('handler')) {
            $newHandler = handlers()->duplicate($handler);

            $this->s('~:selected_handler_id_by_cat_id/' . $this->s('~:selected_cat_id'), $newHandler->id, RA);

            $this->e('ewma/handlers/create')->trigger();
        }
    }

    public function delete()
    {
        if ($this->data('discarded')) {
            $this->c('\std\ui\dialogs~:close:deleteConfirm|ewma/handlers');
        } else {
            if ($handler = $this->unxpackModel('handler')) {
                if ($this->data('confirmed')) {
                    handlers()->delete($handler);

                    $this->e('ewma/handlers/delete', ['handler_id' => $handler->id])->trigger();

                    $this->c('\std\ui\dialogs~:close:deleteConfirm|ewma/handlers');
                } else {
                    $this->c('\std\ui\dialogs~:open:deleteConfirm|ewma/handlers', [
                        'path'          => '@deleteConfirm:view',
                        'data'          => [
                            'handler'      => pack_model($handler),
                            'confirm_call' => $this->_abs(':delete|', ['handler' => $this->data['handler']]),
                            'discard_call' => $this->_abs(':delete|', ['handler' => $this->data['handler']])
                        ],
                        'pluginOptions' => [
                            'resizable' => 'false'
                        ]
                    ]);
                }
            }
        }
    }

    public function arrange()
    {
        foreach ((array)$this->data('sequence') as $n => $id) {
            if ($handler = \ewma\handlers\models\Handler::find($id)) {
                $handler->position = (int)$n * 10;
                $handler->save();
            }
        }

        handlers()->compileIdsByPaths();
    }

    public function rename()
    {
        if ($handler = $this->unpackModel('handler')) {
            $txt = \std\ui\Txt::value($this);

            $handler->name = $txt->value;
            $handler->path = $handler->cat->path . ':' . $handler->name;
            $handler->save();

            handlers()->compileIdsByPaths();

            $txt->response();

            $this->e('ewma/handlers/update/name', ['handler_id' => $handler->id])->trigger(['handler' => $handler]);
        }
    }
}
