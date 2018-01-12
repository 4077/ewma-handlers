<?php namespace ewma\handlers\ui\controllers\main\cats;

class Node extends \Controller
{
    private $cat;

    private $viewInstance;

    public function __create()
    {
        $this->cat = $this->data['cat'];

        $this->viewInstance = $this->cat->id;
    }

    public function reload()
    {
        $this->jquery('|' . $this->viewInstance)->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|' . $this->viewInstance);

        $isRootCat = $this->data['root_cat_id'] == $this->cat->id;

        $cat = $this->cat;

        $catXPack = xpack_model($cat);

        $v->assign([
                       'ROOT_CLASS'       => $isRootCat ? 'root' : '',
                       'NAME'             => $isRootCat
                           ? ''
                           : $this->c('\std\ui txt:view', [
                               'path'                => '>xhr:rename|',
                               'data'                => [
                                   'cat' => $catXPack
                               ],
                               'class'               => 'txt',
                               'fitInputToClosest'   => '.content',
                               'placeholder'         => '...',
                               'editTriggerSelector' => $this->_selector('|' . $this->viewInstance) . " .rename.button",
                               'content'             => $cat->name
                           ]),
                       'RENAME_BUTTON'    => $isRootCat
                           ? ''
                           : $this->c('\std\ui tag:view', [
                               'attrs'   => [
                                   'class' => 'rename button',
                                   'hover' => 'hover',
                                   'title' => 'Переименовать'
                               ],
                               'content' => '<div class="icon"></div>'
                           ]),
                       'EXCHANGE_BUTTON'  => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:exchange|',
                           'data'    => [
                               'cat' => $catXPack
                           ],
                           'class'   => 'button exchange',
                           'title'   => 'Импорт/экспорт',
                           'content' => '<div class="icon"></div>'
                       ]),
                       'CREATE_BUTTON'    => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:create|',
                           'data'    => [
                               'cat' => $catXPack
                           ],
                           'class'   => 'button create',
                           'title'   => 'Создать',
                           'content' => '<div class="icon"></div>'
                       ]),
                       'DUPLICATE_BUTTON' => $this->c('\std\ui button:view', [
                           'visible' => !$isRootCat,
                           'path'    => '>xhr:duplicate|',
                           'data'    => [
                               'cat' => $catXPack
                           ],
                           'class'   => 'button duplicate',
                           'title'   => 'Дублировать',
                           'content' => '<div class="icon"></div>'
                       ]),
                       'DELETE_BUTTON'    => $this->c('\std\ui button:view', [
                           'visible' => !$isRootCat,
                           'path'    => '>xhr:delete|',
                           'data'    => [
                               'cat' => $catXPack
                           ],
                           'class'   => 'button delete',
                           'title'   => 'Удалить',
                           'content' => '<div class="icon"></div>'
                       ]),
                       'COMPILE_BUTTON'        => $this->c('\std\ui button:view', [
                           'visible' => $isRootCat,
                           'path'    => '>xhr:compile|',
                           'data'    => [
                               'cat' => $catXPack
                           ],
                           'class'   => 'button compile',
                           'title'   => 'Скомпилировать',
                           'content' => '<div class="icon"></div>'
                       ])
                   ]);

        $this->css(':\jquery\ui icons');

        if (!$isRootCat) {
            $this->c('\std\ui button:bind', [
                'selector' => $this->_selector('|' . $this->viewInstance),
                'path'     => '>xhr:select|',
                'data'     => [
                    'cat' => $catXPack
                ]
            ]);
        }

        return $v;
    }
}
