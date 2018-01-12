<?php namespace ewma\handlers\ui\controllers\main\cats\node;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function __create()
    {
        $this->a() or $this->lock();
    }

    public function select()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $s = &$this->s('~|');

            $s['selected_cat_id'] = $cat->id;

            $this->e('ewma/handlers/cat_select')->trigger();
        }
    }

    public function create()
    {
        if ($cat = $this->unpackModel('cat')) {
            $newCat = handlers()->cats->create($cat);

            $this->s('~:selected_cat_id', $newCat->id, RR);

            $this->e('ewma/handlers/cats/create', ['cat_id' => $cat->id])->trigger(['cat' => $cat]);
        }
    }

    public function duplicate()
    {
        if ($cat = $this->unpackModel('cat')) {
            $newCat = handlers()->cats->duplicate($cat);

            $s = &$this->s('~|');

            $s['selected_cat_id'] = $newCat->id;

            $this->e('ewma/handlers/cats/create', ['cat_id' => $cat->id])->trigger(['cat' => $cat]);
        }
    }

    public function delete()
    {
        if ($this->data('discarded')) {
            $this->c('\std\ui\dialogs~:close:deleteCatConfirm|ewma/handlers');
        } else {
            if ($cat = $this->unpackModel('cat')) {
                $catsIds = \ewma\Data\Tree::getIds($cat);

                $nestedCatsCount = count($catsIds) - 1;

                $handlers = \ewma\handlers\models\Handler::where('target_type', \ewma\handlers\models\Cat::class)
                    ->whereIn('target_id', $catsIds)
                    ->get();

                $handlersCount = count($handlers);

                if ($this->dataHas('confirmed') || (!$nestedCatsCount && !$handlersCount)) {
                    handlers()->cats->delete($cat);

                    $selectedCatId = &$this->s('~:selected_cat_id|');
                    if (in_array($selectedCatId, $catsIds)) {
                        $selectedCatId = false;
                    }

                    $this->e('ewma/handlers/cats/delete')->trigger();

                    $this->c('\std\ui\dialogs~:close:deleteCatConfirm|ewma/handlers');
                } else {
                    $this->c('\std\ui\dialogs~:open:deleteCatConfirm|ewma/handlers', [
                        'path'          => '~cats/deleteConfirm:view',
                        'data'          => [
                            'confirm_call'      => $this->_abs(':delete|', ['cat' => $this->data['cat']]),
                            'discard_call'      => $this->_abs(':delete|', ['cat' => $this->data['cat']]),
                            'cat_name'          => $cat->name,
                            'handlers_count'    => $handlersCount,
                            'nested_cats_count' => $nestedCatsCount
                        ],
                        'pluginOptions' => [
                            'resizable' => false
                        ]
                    ]);
                }
            }
        }
    }

    public function compile()
    {
        if ($cat = $this->unpackModel('cat')) { // todo compile cat handlers
            handlers()->compileAll();
        }
    }

    public function rename()
    {
        if ($cat = $this->unpackModel('cat')) {
            $txt = \std\ui\Txt::value($this);

            $cat->name = $txt->value;
            $cat->save();

            $txt->response();

            handlers()->updatePaths($cat);

            $this->e('ewma/handlers/cats/update/name', ['cat_id' => $cat->id])->trigger(['cat' => $cat]);
        }
    }

    public function exchange()
    {
        if ($cat = $this->unpackModel('cat')) {
            $this->c('\std\ui\dialogs~:open:exchange|ewma/handlers', [
                'default'             => [
                    'pluginOptions/width' => 500
                ],
                'path'                => '\std\data\exchange~:view|ewma/handlers',
                'data'                => [
                    'target_name' => '#' . $cat->id . ' ' . $cat->path,
                    'import_call' => $this->_abs('<<app:import', ['cat' => pack_model($cat)]),
                    'export_call' => $this->_abs('<<app:export', ['cat' => pack_model($cat)])
                ],
                'pluginOptions/title' => 'handlers'
            ]);
        }
    }
}
