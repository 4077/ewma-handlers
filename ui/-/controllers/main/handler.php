<?php namespace ewma\handlers\ui\controllers\main;

class Handler extends \Controller
{
    public function reload()
    {
        $this->jquery()->replace($this->view());
    }

    public function view()
    {
        $v = $this->v();

        $selectedCatId = $this->s('~:selected_cat_id');

        $handlerId = $this->s('~:selected_handler_id_by_cat_id/' . $selectedCatId);

        if ($handler = \ewma\handlers\models\Handler::find($handlerId)) {
            $v->assign([
                           'PATH'           => $handler->path,
                           'COMPILE_BUTTON' => $this->c('\std\ui button:view', [
                               'path'    => '>xhr:compile',
                               'data'    => [
                                   'handler' => xpack_model($handler)
                               ],
                               'class'   => 'compile_button',
                               'content' => 'Скомпилировать'
                           ]),
                           'HANDLER'        => $this->c('handler~:view', [
                               'handler' => $handler
                           ])
                       ]);

            $this->widget(':');

            $this->e('ewma/handlers/cats/update/name', ['cat_id' => $handler->target_id])->rebind(':reload');
            $this->e('ewma/handlers/update/name', ['handler_id' => $handler->id])->rebind(':reload');
        }

        $this->css(':\css\std~');

        $this->e('ewma/handlers/cat_select')->rebind(':reload');
        $this->e('ewma/handlers/select')->rebind(':reload');

        $this->e('ewma/handlers/cats/create')->rebind(':reload');
        $this->e('ewma/handlers/cats/delete')->rebind(':reload');

        $this->e('ewma/handlers/create')->rebind(':reload');
        $this->e('ewma/handlers/delete')->rebind(':reload');

        return $v;
    }
}
