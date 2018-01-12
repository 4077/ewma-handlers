<?php namespace ewma\handlers\ui\controllers\main\handlers;

class DeleteConfirm extends \Controller
{
    public function view()
    {
        $v = $this->v();

        /**
         * @var $confirmCall \ewma\Controllers\Call
         * @var $discardCall \ewma\Controllers\Call
         */
        $confirmCall = $this->_call($this->data('confirm_call'));
        $discardCall = $this->_call($this->data('discard_call'));

        $confirmCall->data('confirmed', true);
        $discardCall->data('discarded', true);

        $v->assign([
                       'MESSAGE'        => $this->getMessage(),
                       'CONFIRM_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => $confirmCall->path(),
                           'data'    => $confirmCall->data(),
                           'class'   => 'button red',
                           'content' => 'Удалить'
                       ]),
                       'DISCARD_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => $discardCall->path(),
                           'data'    => $discardCall->data(),
                           'class'   => 'button blue',
                           'content' => 'Отмена'
                       ]),
                   ]);

        $this->css(':\css\std~');

        return $v;
    }

    private function getMessage() // todo
    {
        $handler = $this->unpackModel('handler');

        $usingHandlers = handlers()->getUsingHandlers($handler);

//        $message = 'Обрабочик <b>' . $handler->id . ' ' . $handler->path . '</b> используется в узлах следующих обработчиков: ';
//
//        $tmp = [];
//
//        if ($usingHandlers) {
//            $tmp[] = $this->data['nested_cats_count'] . ' подкатегори' . ending($this->data['nested_cats_count'], 'ю', 'и', 'й');
//
//            $tail = 'Все подкатегории будут удалены.';
//        }
//
//        if ($this->data['handlers_count']) {
//            $tmp[] = $this->data['handlers_count'] . ' обработчик' . ending($this->data['handlers_count'], '', 'а', 'ов');
//
//            $tail = 'Все обработчики будут удалены.';
//        }
//
//        if ($this->data['nested_cats_count'] && $this->data['handlers_count']) {
//            $tail = 'Все подкатегории и обработчики будут удалены.';
//        }
//
//        if ($tmp) {
//            $message .= implode(' и ', $tmp) . '.';
//        }

//        $message .= '<br>' . $tail;

        return 'Удалить обработчик <b>' . $handler->path . '</b>';
    }
}
