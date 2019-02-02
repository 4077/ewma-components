<?php namespace ewma\components;

class Svc extends \ewma\service\Service
{
    /**
     * @var self
     */
    public static $instance;

    /**
     * @return \ewma\components\Svc
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new self;
            static::$instance->__register__();
        }

        return static::$instance;
    }

    protected $services = ['cats'];

    /**
     * @var $cats \ewma\components\Svc\Cats
     */
    public $cats = \ewma\components\Svc\Cats::class;

    //
    //
    //

    public function compile(\ewma\components\models\Component $component)
    {
        $handlers = $this->getHandlers($component);

        foreach ($handlers as $handler) {
            handlers()->compile($handler);
        }
    }

    public function getHandlers(\ewma\components\models\Component $component)
    {
        return $component->handlers()->orderBy('instance')->get();
    }

    private $handlersByComponentIdAndInstance = [];

    public function getHandler(\ewma\components\models\Component $component, $instance = null)
    {
        if (!isset($this->handlersByComponentIdAndInstance[$component->id][$instance])) {
            $this->handlersByComponentIdAndInstance[$component->id][$instance] = $component->handlers($instance)->first();
        }

        return $this->handlersByComponentIdAndInstance[$component->id][$instance];
    }

    public function getFullName(\ewma\components\models\Component $component, $catsIdsFilter = false)
    {
        $branch = \ewma\Data\Tree::getBranch($component->cat);

        array_shift($branch);

        if (is_array($catsIdsFilter) && !empty($catsIdsFilter)) {
            $branch = array_filter($branch, function ($cat) use ($catsIdsFilter) {
                return in_array($cat->id, $catsIdsFilter);
            });
        }

        $catBranch = table_column($branch, 'name');

        return implode('/', $catBranch) . ' → ' . $component->name;
    }

    public function create(\ewma\components\models\Cat $cat)
    {
        $component = $cat->components()->create([]);

        $this->createHandler($component, 'ui');

        return $component;
    }

    public function createHandler(\ewma\components\models\Component $component, $instance = '')
    {
        $handler = handlers()->create();
        $handler->instance = $instance;

        $component->handlers()->save($handler);

        return $handler;
    }

    public function duplicate(\ewma\components\models\Component $component)
    {
        return $this->import($this->export($component));
    }

    public function delete(\ewma\components\models\Component $component)
    {
        $handlers = $this->getHandlers($component);

        foreach ($handlers as $handler) {
            handlers()->delete($handler);
        }

        $component->delete();
    }

    public function export(\ewma\components\models\Component $component)
    {
        $output['component'] = $component->toArray();

        $handlers = $this->getHandlers($component);

        foreach ($handlers as $handler) {
            $output['handlers'][] = handlers()->export($handler);
        }

        return $output;
    }

    public function import($data)
    {
        $newComponent = \ewma\components\models\Component::create($data['component']);

        if (!empty($data['handlers'])) {
            foreach ($data['handlers'] as $handlerData) {
                $handlerData['handler']['target_id'] = $newComponent->id;

                handlers()->import($handlerData);
            }
        }

        return $newComponent;
    }

    public function getUsages(\ewma\components\models\Component $component) // todo
    {

    }
}
