<?php namespace ewma\handlers\Svc;

class Compiler
{
    public function compile(\ewma\handlers\models\Handler $handler)
    {
        $rootNode = handlers()->getRootNode($handler);

        $nodeData = _j($rootNode->data);

        return [
            'path'         => $handler->path,
            'combine_mode' => $nodeData['combine_mode'],
            'nodes'        => $this->compileNodes($rootNode)
        ];
    }

    private function compileNodes($node) // root, input, data_modifier
    {
        $output = [];

        $nestedNodes = $node->nested()->where('enabled', true)->orderBy('position')->get();

        foreach ($nestedNodes as $nestedNode) {
            if ($nestedNode->type == 'CALL') {
                $output[] = $this->compileCall($nestedNode);
            }

            if ($nestedNode->type == 'VALUE') {
                $output[] = $this->compileValue($nestedNode);
            }

            if ($nestedNode->type == 'HANDLER') {
                $output[] = $this->compileHandler($nestedNode);
            }

            if ($nestedNode->type == 'DATA_MODIFIER') {
                $output[] = $this->compileDataModifier($nestedNode);
            }
        }

        return $output;
    }

    private function compileHandler($node)
    {
        $nodes = [];

        $nestedNodes = $node->nested()->where('enabled', true)->orderBy('position')->get();

        foreach ($nestedNodes as $nestedNode) {
            if ($nestedNode->type == 'DATA_MODIFIER') {
                $nodes[] = $this->compileDataModifier($nestedNode);
            }

            if ($nestedNode->type == 'INPUT') {
                $nodes[] = $this->compileInput($nestedNode);
            }
        }

        $nodesData = _j($node->data);

        return [
            'type'     => 'HANDLER',
            'required' => (int)$node->required,
            'source'   => $nodesData['source'],
            'mappings' => $nodesData['mappings'],
            'nodes'    => $nodes
        ];
    }

    private function compileCall($node)
    {
        $nodes = [];

        $nestedNodes = $node->nested()->where('enabled', true)->orderBy('position')->get();

        foreach ($nestedNodes as $nestedNode) {
            if ($nestedNode->type == 'DATA_MODIFIER') {
                $nodes[] = $this->compileDataModifier($nestedNode);
            }

            if ($nestedNode->type == 'INPUT') {
                $nodes[] = $this->compileInput($nestedNode);
            }
        }

        $nodeData = _j($node->data);

        return [
            'type'     => 'CALL',
            'required' => (int)$node->required,
            'path'     => $nodeData['path'],
            'nodes'    => $nodes
        ];
    }

    private function compileDataModifier($node)
    {
        $nodeData = _j($node->data);

        return [
            'type'         => 'DATA_MODIFIER',
            'required'     => (int)$node->required,
            'path'         => $nodeData['path'],
            'combine_mode' => $nodeData['combine_mode'],
            'nodes'        => $this->compileNodes($node)
        ];
    }

    private function compileInput($node)
    {
        $nodeData = _j($node->data);

        return [
            'type'         => 'INPUT',
            'required'     => (int)$node->required,
            'path'         => $nodeData['path'],
            'combine_mode' => $nodeData['combine_mode'],
            'nodes'        => $this->compileNodes($node)
        ];
    }

    private function compileValue($node)
    {
        $nodesData = _j($node->data);

        return [
            'type'  => 'VALUE',
            'value' => $nodesData['value'][$nodesData['type']]
        ];
    }
}
