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

    public function getRootCat()
    {
        if (!$node = \ewma\handlers\models\Cat::where('parent_id', 0)->first()) {
            $node = \ewma\handlers\models\Cat::create([
                                                          'parent_id' => 0
                                                      ]);
        }

        return $node;
    }

    public function create(\ewma\handlers\models\Cat $cat)
    {
        return $cat->nested()->create([]);
    }

    public function duplicate(\ewma\handlers\models\Cat $cat)
    {
        $newCat = \ewma\handlers\models\Cat::create($cat->toArray());

        $this->import($newCat, $this->export($cat), true);

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
            $this->exportOutput['handlers'][$cat->id][] = $this->svc->export($handler);
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
                $handlerData['handler']['target_id'] = $newCat->id;

                $this->svc->import($handlerData);
            }
        }

        if (!empty($importData['cats']['ids_by_parent'][$catId])) {
            foreach ($importData['cats']['ids_by_parent'][$catId] as $sourceCatId) {
                $this->importRecursion($newCat, $importData, $sourceCatId);
            }
        }
    }
}
