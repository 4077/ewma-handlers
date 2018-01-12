<?php namespace ewma\handlers\Svc;

class Nodes extends \ewma\service\Service
{
    protected $services = ['svc'];

    /**
     * @var $svc \ewma\handlers\Svc
     */
    public $svc = \ewma\handlers\Svc::class;

    //
    //
    //

    private $assignMap = [
        'CALL'          => 'ROOT, INPUT, DATA_MODIFIER',
        'HANDLER'       => 'ROOT, INPUT, DATA_MODIFIER',
        'VALUE'         => 'ROOT, INPUT, DATA_MODIFIER',
        'DATA_MODIFIER' => 'ROOT, INPUT, DATA_MODIFIER, CALL, HANDLER',
        'INPUT'         => 'CALL, HANDLER'
    ];

    public function canBeAssigned($sourceType, $targetType)
    {
        return in($targetType, $this->assignMap[$sourceType]);
    }

    public function createHandler(\ewma\handlers\models\Node $node)
    {
        if ($this->canBeAssigned('HANDLER', $node->type)) {
            $newNode = $node->nested()->create([
                                                   'handler_id' => $node->handler_id,
                                                   'type'       => 'HANDLER',
                                                   'data'       => j_([
                                                                          'source'   => '',
                                                                          'mappings' => '*'
                                                                      ])
                                               ]);

            return $newNode;
        }
    }

    public function createCall(\ewma\handlers\models\Node $node)
    {
        if ($this->canBeAssigned('CALL', $node->type)) {
            $newNode = $node->nested()->create([
                                                   'handler_id' => $node->handler_id,
                                                   'type'       => 'CALL',
                                                   'data'       => j_([
                                                                          'name' => '',
                                                                          'path' => '',
                                                                          'desc' => ''
                                                                      ])
                                               ]);

            return $newNode;
        }
    }

    public function createDataModifier(\ewma\handlers\models\Node $node)
    {
        if ($this->canBeAssigned('DATA_MODIFIER', $node->type)) {
            $newNode = $node->nested()->create([
                                                   'handler_id' => $node->handler_id,
                                                   'type'       => 'DATA_MODIFIER',
                                                   'data'       => j_([
                                                                          'name'         => '',
                                                                          'path'         => '',
                                                                          'combine_mode' => 'first'
                                                                      ])
                                               ]);

            return $newNode;
        }
    }

    public function createInput(\ewma\handlers\models\Node $node)
    {
        if ($this->canBeAssigned('INPUT', $node->type)) {
            $newNode = $node->nested()->create([
                                                   'handler_id' => $node->handler_id,
                                                   'type'       => 'INPUT',
                                                   'data'       => j_([
                                                                          'name'         => '',
                                                                          'path'         => '',
                                                                          'desc'         => '',
                                                                          'combine_mode' => 'first'
                                                                      ])
                                               ]);

            return $newNode;
        }
    }

    public function createValue(\ewma\handlers\models\Node $node)
    {
        if ($this->canBeAssigned('VALUE', $node->type)) {
            $newNode = $node->nested()->create([
                                                   'handler_id' => $node->handler_id,
                                                   'type'       => 'VALUE',
                                                   'data'       => j_([
                                                                          'name'  => '',
                                                                          'type'  => 'string',
                                                                          'value' => [
                                                                              'string' => '',
                                                                              'array'  => [],
                                                                              'bool'   => false
                                                                          ]
                                                                      ])
                                               ]);

            return $newNode;
        }
    }

    public function getData(\ewma\handlers\models\Node $node, $path)
    {
        $data = _j($node->data);

        return ap($data, $path);
    }

    public function updateData(\ewma\handlers\models\Node $node, $path, $value)
    {
        $data = _j($node->data);

        ap($data, $path, $value);

        $node->data = j_($data);
        $node->save();

        return $node;
    }

    public function setHandlerSource(\ewma\handlers\models\Node $node, $source)
    {
        if ($this->isHandler($node)) {
            handlers()->nodes->updateData($node, 'source', $source);

            if (is_numeric($source)) {
                $node->source_handler_id = $source;
            } else {
                $node->source_handler_id = null;
            }

            $node->save();
        }
    }

    public function isContainer(\ewma\handlers\models\Node $node)
    {
        return $node->type == 'INPUT' || $node->type == 'DATA_MODIFIER' || $node->type == 'ROOT';
    }

    public function isReturner(\ewma\handlers\models\Node $node)
    {
        return $node->type == 'CALL' || $node->type == 'HANDLER';
    }

    public function isValue(\ewma\handlers\models\Node $node)
    {
        return $node->type == 'VALUE';
    }

    public function isHandler(\ewma\handlers\models\Node $node)
    {
        return $node->type == 'HANDLER';
    }

    public function setCombineMode(\ewma\handlers\models\Node $node, $mode)
    {
        if (in($mode, array_keys($this->svc->containersCombineModes)) && $this->isContainer($node)) {
            $this->updateData($node, 'combine_mode', $mode);

            return $node;
        }
    }

    public function setValueType(\ewma\handlers\models\Node $node, $type)
    {
        if (in($type, array_keys($this->svc->valuesTypes)) && $this->isValue($node)) {
            $this->updateData($node, 'type', $type);

            return $node;
        }
    }

    private $deleted;

    public function delete(\ewma\handlers\models\Node $node)
    {
        $this->deleted = [];

        \DB::transaction(function () use ($node) {
            $this->deleteRecursion($node);
        });

        return $this->deleted;
    }

    private function deleteRecursion(\ewma\handlers\models\Node $node)
    {
        foreach ($node->nested as $nestedNode) {
            $this->deleteRecursion($nestedNode);
        }

        $this->deleted[] = $node->id;

        $node->delete();
    }

    public function export(\ewma\handlers\models\Node $node)
    {
        $treeBuilder = \ewma\handlers\models\Node::where('handler_id', $node->handler_id)->orderBy('position');

        $tree = \ewma\Data\Tree::get($treeBuilder);

        return [
            'node_id'   => $node->id,
            'node_type' => $node->type,
            'nodes'     => $tree->getFlattenData($node->id),
        ];
    }

    public function import(\ewma\handlers\models\Node $target, $data, $skipFirstLevel = false)
    {
        \DB::transaction(function () use ($target, $data, $skipFirstLevel) {
            $this->importRecursion($target, $data, $data['node_id'], $skipFirstLevel);
        });
    }

    private function importRecursion(\ewma\handlers\models\Node $target, $data, $nodeId, $skipFirstLevel = false)
    {
        $newNode = false;

        if ($skipFirstLevel) {
            $newNode = $target;
        } else {
            $newNodeData = $data['nodes']['nodes_by_id'][$nodeId];

            if ($newNodeData instanceof \Model) {
                $newNodeData = $newNodeData->toArray();
            }

            if ($this->canBeAssigned($newNodeData['type'], $target->type)) {
                $newNodeData['handler_id'] = $target->handler_id;

                $newNode = $target->nested()->create($newNodeData);
            }
        }

        if ($newNode && !empty($data['nodes']['ids_by_parent'][$nodeId])) {
            foreach ($data['nodes']['ids_by_parent'][$nodeId] as $sourceNodeId) {
                $this->importRecursion($newNode, $data, $sourceNodeId);
            }
        }
    }
}
