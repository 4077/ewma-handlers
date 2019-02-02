<?php namespace ewma\handlers\ui\handler\nodes\controllers\main;

class DataModifier extends \Controller
{
    private $node;

    public function __create()
    {
        if ($node = $this->unpackModel('node')) {
            $this->node = $node;

            $this->instance_($this->node->id);
        } else {
            $this->lock();
        }
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());

        $this->c('~app:bindBarsClick');
    }

    public function view()
    {
        $v = $this->v('|');

        $node = $this->node;

        $nodeData = _j($node->data);

        $requestsData = ['node' => xpack_model($node)];

        $v->assign([
                       'NODE_ID'        => $node->id,
                       'DISABLED_CLASS' => $node->enabled ? '' : 'disabled',
                       'TYPE_CLASS'     => strtolower($node->type),
                       'PATH_TXT'       => $this->c('\std\ui txt:view', [
                           'path'              => '>xhr:updatePath',
                           'data'              => $requestsData,
                           'class'             => 'txt',
                           'title'             => 'Путь к узлу данных в текущей области видимости',
                           'placeholder'       => '...',
                           'fitInputToClosest' => '.container',
                           'content'           => $nodeData['path']
                       ]),
                       'CP'             => $this->c('>cp:view', ['node' => $node]),
                       'NODES'          => $this->c('~:view', ['node' => $node])
                   ]);

        if ($node['required']) {
            $v->assign('required');
        }

        if ($nodeData['combine_mode']) {
            $v->assign('combine_mode', [
                'VALUE' => $nodeData['combine_mode']
            ]);
        }

        $this->css();

        $this->css(':\js\jquery\ui icons');

        $this->e('ewma/handlers/nodes/update', ['type' => 'data_modifier'])->rebind(':reload');

        return $v;
    }
}
