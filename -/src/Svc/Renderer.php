<?php namespace ewma\handlers\Svc;

class Renderer
{
    private $handlerData = [];

    public function render($cache, $data = [])
    {
        $this->handlerData = $data;

        return $this->renderContainer($cache);
    }

    private function renderContainer($container) // root, data_modifier, input
    {
        $combineMode = $container['combine_mode'];
        $nodes = $container['nodes'];

        $output = null;

        foreach ($nodes as $node) {
            if ($node['type'] == 'DATA_MODIFIER') {
                $renderedContainer = $this->renderContainer($node);

                if (null === $renderedContainer && !empty($node['required'])) {
                    return null;
                } else {
                    ap($this->handlerData, $node['path'], $renderedContainer);

                    continue;
                }
            }

            $renderedNode = $this->renderNode($node);

            if (null === $renderedNode) {
                if (!empty($node['required'])) {
                    return null;
                }
            } else {
                if ($combineMode == 'first') {
                    return $renderedNode;
                }

                if ($combineMode == 'concat' && is_scalar($renderedNode)) {
                    $output .= $renderedNode;
                }

                if ($combineMode == 'array') {
                    $output[] = $renderedNode;
                }

                if ($combineMode == 'add' && (is_scalar($renderedNode) || is_array($renderedNode))) {
                    if (!isset($addCombineModeType)) {
                        if (is_scalar($renderedNode)) {
                            $addCombineModeType = 'scalar';
                        } else {
                            $addCombineModeType = 'array';
                        }
                    }

                    if (is_scalar($renderedNode) && $addCombineModeType == 'scalar') {
                        if (is_null($output)) {
                            $output = '';
                        }

                        $output += $renderedNode;
                    }

                    if (is_array($renderedNode) && $addCombineModeType == 'array') {
                        if (is_null($output)) {
                            $output = [];
                        }

                        $output += $renderedNode;
                    }
                }

                if ($combineMode == 'aa' && is_array($renderedNode)) {
                    if (is_null($output)) {
                        $output = [];
                    }

                    aa($output, $renderedNode);
                }

                if ($combineMode == 'ra' && is_array($renderedNode)) {
                    if (is_null($output)) {
                        $output = [];
                    }

                    ra($output, $renderedNode);
                }
            }
        }

        return $output;
    }

    private function renderNode($node) // handler, call, value
    {
        if ($node['type'] == 'HANDLER') {
            $rendered = $this->renderHandler($node['source'], $node['nodes'], $node['mappings']);
        }

        if ($node['type'] == 'CALL') {
            $rendered = $this->renderCall($node['path'], $node['nodes']);
        }

        if ($node['type'] == 'VALUE') {
            $rendered = $this->renderValue($node['value']);
        }

        return $rendered ?? null;
    }

    private function renderHandler($source, $nodes, $mappings)
    {
        $data = [];

        remap($data, $this->handlerData, $mappings);

        foreach ($nodes as $node) {
            $renderedContainer = $this->renderContainer($node);

            if ($node['required'] && null === $renderedContainer) {
                return null;
            }

            if ($node['type'] == 'INPUT') {
                ap($data, $node['path'], $renderedContainer);
            }

            if ($node['type'] == 'DATA_MODIFIER') {
                ap($this->handlerData, $node['path'], $renderedContainer);
            }
        }

        if (false !== strpos($source, '{')) {
            $source = $this->tokenize($source);
        }

        return handlers()->render($source, $data);
    }

    private function renderCall($path, $nodes)
    {
        $handlerDataBackup = $this->handlerData;

        $data = [];

        foreach ($nodes as $node) {
            $renderedContainer = $this->renderContainer($node);

            if ($node['required'] && null === $renderedContainer) {
                return null;
            }

            if ($node['type'] == 'INPUT') {
                ap($data, $node['path'], $renderedContainer);
            }

            if ($node['type'] == 'DATA_MODIFIER') {
                ap($this->handlerData, $node['path'], $renderedContainer);
            }
        }

        $this->handlerData = $handlerDataBackup;

        if (false !== strpos($path, '{')) {
            $path = $this->tokenize($path);
        }

        $output = appc($path, $data);

        if ($output instanceof \ewma\Views\View) {
            return $output->render();
        } else {
            return $output;
        }
    }

    private function renderValue($value)
    {
        return $this->tokenize($value);
    }

    private function tokenize($value)
    {
        if (is_scalar($value)) {
            if (preg_match('/^\{([\w\/_-]*)\}$/', trim($value), $dataModifierPathMatch)) {
                $outputValue = ap($this->handlerData, $dataModifierPathMatch[1]);
            } else {
                $outputValue = $value;

                foreach (a2f($this->handlerData) as $dataModifierPath => $dataModifierValue) {
                    if (is_scalar($dataModifierValue)) {
                        $outputValue = str_replace('{' . $dataModifierPath . '}', $dataModifierValue, $outputValue);
                    }
                }

                $outputValue = preg_replace('/\{.+\}/', '', $outputValue);
            }

            return $outputValue;
        } else {
            return $value;
        }
    }
}
