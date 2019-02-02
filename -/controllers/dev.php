<?php namespace ewma\handlers\controllers;

class Dev extends \Controller
{
    public function getOrphanedTargetsPacks()
    {
        $handlers = \ewma\handlers\models\Handler::all();

        $packs = [];

        foreach ($handlers as $handler) {
            if (!$handler->target) {
                $packs[] = [
                    $handler->target_type . ':' . $handler->target_id
                ];
            }
        }

        return $packs;
    }

    public function deleteOrphanedHandlers()
    {
        $handlers = \ewma\handlers\models\Handler::all();

        $deletedCount = 0;

        foreach ($handlers as $handler) {
            if (!$handler->target) {
                handlers()->delete($handler);

                $deletedCount++;
            }
        }

        return 'deleted handlers: ' . $deletedCount;
    }
}
