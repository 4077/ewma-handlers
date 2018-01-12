<?php namespace ewma\handlers\Svc;

class Cats extends \ewma\service\Service
{
    protected $services = ['svc'];

    /**
     * @var $svc \ewma\handlers\Svc
     */
    public $svc = \ewma\handlers\Svc::class;

    //
    //
    //

    public function create(\ewma\handlers\models\Cat $cat)
    {
        return $cat->nested()->create([]);
    }

    public function duplicate(\ewma\handlers\models\Cat $cat)
    {
        $tree = \ewma\Data\Tree::get(
            \ewma\handlers\models\Cat::orderBy('position')
        );

        $newCat = $this->duplicateRecursion($tree, $cat);

        return $newCat;
    }

    private function duplicateRecursion(\ewma\Data\Tree $tree, $cat, $parentCat = null)
    {
        $newCatData = $cat->toArray();
        if (null !== $parentCat) {
            $newCatData['parent_id'] = $parentCat->id;
        }

        $newCat = \ewma\handlers\models\Cat::create($newCatData);

        $handlers = $cat->handlers()->orderBy('position')->get();
        foreach ($handlers as $handler) {
            $newHandler = handlers()->duplicate($handler);

            $newHandler->target_id = $newCat->id;
            $newHandler->save();
        }

        $subcats = $tree->getSubnodes($cat->id);
        foreach ($subcats as $subcat) {
            $this->duplicateRecursion($tree, $subcat, $newCat);
        }

        return $newCat;
    }

    public function delete(\ewma\handlers\models\Cat $cat)
    {
        $catsIds = \ewma\Data\Tree::getIds($cat);

        $handlers = \ewma\handlers\models\Handler::where('target_type', \ewma\handlers\models\Cat::class)
            ->whereIn('target_id', $catsIds)
            ->get();

        $handlersIds = table_ids($handlers);

        \ewma\handlers\models\Cat::whereIn('id', $catsIds)->delete();

        \ewma\handlers\models\Handler::where('target_type', \ewma\handlers\models\Cat::class)
            ->whereIn('target_id', $catsIds)->delete();

        \ewma\handlers\models\Node::whereIn('handler_id', $handlersIds)->delete();
    }

    private $exportOutput;

    public function export(\ewma\handlers\models\Cat $cat)
    {
        $tree = \ewma\Data\Tree::get(\ewma\handlers\models\Cat::orderBy('position'));

        $this->exportOutput['cat_id'] = $cat->id;
        $this->exportOutput['cats'] = $tree->getFlattenData($cat->id);

        $this->exportRecursion($tree, $cat);

        return $this->exportOutput;
    }

    private function exportRecursion(\ewma\Data\Tree $tree, \ewma\handlers\models\Cat $cat)
    {
        $handlers = $cat->handlers()->orderBy('position')->get();
        foreach ($handlers as $handler) {
            $this->exportOutput['handlers'][$cat->id][] = [
                'handler' => $handler->toArray(),
                'nodes'   => handlers()->nodes->export(handlers()->getRootNode($handler))
            ];
        }

        $subcats = $tree->getSubnodes($cat->id);
        foreach ($subcats as $subcat) {
            $this->exportRecursion($tree, $subcat);
        }
    }

    public function import(\ewma\handlers\models\Cat $target, $data, $skipFirstLevel = false)
    {
        $this->importRecursion($target, $data, $data['cat_id'], $skipFirstLevel);

        handlers()->updatePaths($target);
    }

    private function importRecursion(\ewma\handlers\models\Cat $target, $importData, $catId, $skipFirstLevel = false)
    {
        if ($skipFirstLevel) {
            $newCat = $target;
        } else {
            $newCatData = $importData['cats']['nodes_by_id'][$catId];

            if ($newCatData instanceof \Model) {
                $newCatData = $newCatData->toArray();
            }

            $newCat = $target->nested()->create($newCatData);
        }

        if (!empty($importData['handlers'][$catId])) {
            foreach ($importData['handlers'][$catId] as $handlerData) {
                $newHandler = $newCat->handlers()->create($handlerData['handler']);

                handlers()->nodes->import(handlers()->getRootNode($newHandler), $handlerData['nodes'], true);
            }
        }

        if (!empty($importData['cats']['ids_by_parent'][$catId])) {
            foreach ($importData['cats']['ids_by_parent'][$catId] as $sourceCatId) {
                $this->importRecursion($newCat, $importData, $sourceCatId);
            }
        }
    }

}
