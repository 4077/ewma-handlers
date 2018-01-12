<?php namespace ewma\handlers\ui\controllers\main\cats;

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

    private function getMessage()
    {
        $message = 'Категория <b>' . $this->data['cat_name'] . '</b> содержит ';

        $tmp = [];

        if ($this->data['nested_cats_count']) {
            $tmp[] = $this->data['nested_cats_count'] . ' подкатегори' . ending($this->data['nested_cats_count'], 'ю', 'и', 'й');

            $tail = 'Все подкатегории будут удалены.';
        }

        if ($this->data['handlers_count']) {
            $tmp[] = $this->data['handlers_count'] . ' обработчик' . ending($this->data['handlers_count'], '', 'а', 'ов');

            $tail = 'Все обработчики будут удалены.';
        }

        if ($this->data['nested_cats_count'] && $this->data['handlers_count']) {
            $tail = 'Все подкатегории и обработчики будут удалены.';
        }

        // todo использующие обработчики

        if ($tmp) {
            $message .= implode(' и ', $tmp) . '.';
        }

        $message .= '<br>' . $tail;

        return $message;
    }
}
