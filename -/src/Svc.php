<?php namespace ewma\handlers;

class Svc extends \ewma\service\Service
{
    /**
     * @var self
     */
    public static $instance;

    /**
     * @return \ewma\handlers\Svc
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new self;
            static::$instance->__register__();
        }

        return static::$instance;
    }

    protected $services = ['cats', 'nodes'];

    /**
     * @var $cats \ewma\handlers\Svc\Cats
     */
    public $cats = \ewma\handlers\Svc\Cats::class;

    /**
     * @var $nodes \ewma\handlers\Svc\Nodes
     */
    public $nodes = \ewma\handlers\Svc\Nodes::class;

    //
    //
    //

    public function compileAll()
    {
        clean_dir(abs_path('cache/handlers'));

        $handlers = \ewma\handlers\models\Handler::all();

        foreach ($handlers as $handler) {
            $this->compile($handler);
        }

        $this->compileIdsByPaths();

        return count($handlers);
    }

    public function compile(\ewma\handlers\models\Handler $handler)
    {
        $compiler = new \ewma\handlers\Svc\Compiler;

        awrite(abs_path('cache/handlers/id/' . $handler->id . '.php'), $compiler->compile($handler));
    }

    public function compileSource($handlerSource)
    {
        $handlers = \ewma\handlers\models\Handler::where('path', $handlerSource)->get();

        foreach ($handlers as $handler) {
            $this->compile($handler);
        }
    }

    public function compileIdsByPaths()
    {
        $handlers = \ewma\handlers\models\Handler::orderBy('position')->get();

        $handlersIdsByPath = [];

        foreach ($handlers as $handler) {
            if (!isset($handlersIdsByPath[$handler->path])) {
                $handlersIdsByPath[$handler->path] = $handler->id;
            }
        }

        awrite(abs_path('cache/handlers/ids_by_paths.php'), $handlersIdsByPath);
    }

    private $handlersCache = [];

    public function getHandlerCache($handlerId)
    {
        if (!isset($this->handlersCache[$handlerId])) {
            $this->handlersCache[$handlerId] = aread(abs_path('cache/handlers/id/' . $handlerId . '.php'));
        }

        return $this->handlersCache[$handlerId];
    }

    public function render($handlerSource, $data = [])
    {
        if (is_numeric($handlerSource)) {
            $handlerId = $handlerSource;
        }

        if (is_string($handlerSource)) {
            $handlerId = $this->getHandlerIdByPath($handlerSource);
        }

        if ($handlerSource instanceof \ewma\handlers\models\Handler) {
            $handlerId = $handlerSource->id;
        }

        if (!empty($handlerId)) {
            if ($handlerCache = $this->getHandlerCache($handlerId)) {
                $renderer = new \ewma\handlers\Svc\Renderer;

                return $renderer->render($handlerCache, $data);
            }
        }
    }

    private $handlersIdsByPaths;

    private function getHandlerIdByPath($path)
    {
        if (null === $this->handlersIdsByPaths) {
            $this->handlersIdsByPaths = aread(abs_path('cache/handlers/ids_by_paths.php')) ?? false;
        }

        return $this->handlersIdsByPaths[$path] ?? false;
    }

    public function updatePaths(\ewma\handlers\models\Cat $cat)
    {
        $tree = \ewma\Data\Tree::get($cat);

        $this->updatePathsRecursion($tree, $cat);
        $this->compileIdsByPaths();
    }

    private function updatePathsRecursion(\ewma\Data\Tree $tree, $cat)
    {
        $branch = $tree->getBranch($cat);
        $segments = table_cells_by_id($branch, 'name');
        array_shift($segments);
        $path = a2p($segments);

        $cat->path = $path;
        $cat->save();

        $cat->handlers->each(function ($handler) use ($cat, $path) {
            $handler->path = $path . ':' . $handler->name;
            $handler->cat_position = $cat->position;
            $handler->save();
        });

        $subnodes = $tree->getSubnodes($cat->id);
        foreach ($subnodes as $subnode) {
            $this->updatePathsRecursion($tree, $subnode);
        }
    }

    public function get($handlerSource)
    {
        if (is_numeric($handlerSource)) {
            $handlerId = $handlerSource;
        }

        if (is_string($handlerSource)) {
            $handlerId = $this->getHandlerIdByPath($handlerSource);
        }

        if (!empty($handlerId)) {
            return \ewma\handlers\models\Handler::find($handlerId);
        }
    }

    public function create()
    {
        $handler = \ewma\handlers\models\Handler::create([]);

        $this->getRootNode($handler);

        return $handler;
    }

    public function duplicate(\ewma\handlers\models\Handler $handler)
    {
        $newHandler = \ewma\handlers\models\Handler::create($handler->toArray());

        $handlerRootNode = $this->getRootNode($handler);
        $newHandlerRootNode = $this->getRootNode($newHandler);

        $this->nodes->import($newHandlerRootNode, $this->nodes->export($handlerRootNode), true);

        return $newHandler;
    }

    public function delete(\ewma\handlers\models\Handler $handler, $withUsages = false)
    {
        $handler->nodes()->delete();
        $handler->delete();

        handlers()->compileIdsByPaths();

        if ($withUsages) {
            \ewma\handlers\models\Node::where('type', 'HANDLER')->where('source_handler_id', $handler->id)->delete();
        }
    }

    public function getUsingHandlers(\ewma\handlers\models\Handler $handler)
    {
        $nodesUsingHandler = \ewma\handlers\models\Node::where('type', 'HANDLER')->where('source_handler_id', $handler->id)->get();

        $usingHandlersIds = [];
        foreach ($nodesUsingHandler as $node) {
            merge($usingHandlersIds, $node->handler_id);
        }

        return \ewma\handlers\models\Handler::whereIn('id', $usingHandlersIds)->get();
    }

    public function getRootNode(\ewma\handlers\models\Handler $handler)
    {
        if (!$node = $handler->nodes()->where('type', 'ROOT')->first()) {
            $node = $handler->nodes()->create([
                                                  'type' => 'ROOT',
                                                  'data' => j_([
                                                                   'combine_mode' => 'concat'
                                                               ])
                                              ]);
        }

        return $node;
    }

    public $containersCombineModes = [
        'first'  => 'Будет использовано первое не null значение. Остальные значения обрабатываться не будут',
        'concat' => 'Все вложенные значения скалярных типов будут объединены в строку. Значения других типов будут проигнорированы.',
        'add'    => 'Вложенные значения будут объединены с помощью оператора +. В объединении смогут участвовать либо значения скалярных типов, либо массивы. В зависимости от того, какой из этих типов встретится раньше. Значения других типов будут проигнорированы.',
        'array'  => 'Все вложенные значения будут собраны в массив',
        'aa'     => 'Принимаются только массивы, каждый следующий массив дополняет предыдущие несуществующими узлами.',
        'ra'     => 'Принимаются только массивы, каждый следующий массив дополняет предыдущие несуществующими узлами и перезаписывает существующие.'
    ];

    public $valuesTypes = [
        'bool'   => 'Двоичное значение',
        'string' => 'Строка',
        'array'  => 'Массив'
    ];

    public static $assignmentsTypes = [
        'HANDLER'       => 'Обработчик',
        'CALL'          => 'Вызов',
        'VALUE'         => 'Значение',
        'DATA_MODIFIER' => 'Модификатор данных обработчика',
        'INPUT'         => 'Вход',
    ];
}
