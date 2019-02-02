<?php namespace ewma\components\Svc;

class Cats extends \ewma\service\Service
{
    protected $services = ['svc'];

    /**
     * @var $svc \ewma\components\Svc
     */
    public $svc = \ewma\components\Svc::class;

    //
    //
    //

    public function getRootCat()
    {
        if (!$node = \ewma\components\models\Cat::where('parent_id', 0)->first()) {
            $node = \ewma\components\models\Cat::create([
                                                            'parent_id' => 0
                                                        ]);
        }

        return $node;
    }

    private $catsTree;

    /**
     * @param $treeId
     *
     * @return \ewma\Data\Tree
     */
    public function getTree()
    {
        if (empty($this->catsTree)) {
            $this->catsTree = \ewma\Data\Tree::get(\ewma\components\models\Cat::orderBy('position'));
        }

        return $this->catsTree;
    }

    public function create(\ewma\components\models\Cat $cat)
    {
        return $cat->nested()->create([]);
    }

    public function duplicate(\ewma\components\models\Cat $cat)
    {
        $newCat = \ewma\components\models\Cat::create($cat->toArray());

        $this->import($newCat, $this->export($cat), true);

        return $newCat;
    }

    private function duplicateRecursion(\ewma\Data\Tree $tree, $cat, $parentCat = null)
    {
        $newCatData = $cat->toArray();
        if (null !== $parentCat) {
            $newCatData['parent_id'] = $parentCat->id;
        }

        $newCat = \ewma\components\models\Cat::create($newCatData);

        $components = $cat->components()->orderBy('position')->get();
        foreach ($components as $component) {
            $newComponent = components()->duplicate($component);

            $newComponent->target_id = $newCat->id;
            $newComponent->save();
        }

        $subcats = $tree->getSubnodes($cat->id);
        foreach ($subcats as $subcat) {
            $this->duplicateRecursion($tree, $subcat, $newCat);
        }

        return $newCat;
    }

    public function delete(\ewma\components\models\Cat $cat)
    {
        $catsIds = \ewma\Data\Tree::getIds($cat);

        \ewma\components\models\Cat::whereIn('id', $catsIds)->delete();

        $componentsBuilder = \ewma\components\models\Component::whereIn('cat_id', $catsIds);

        $handlers = \ewma\handlers\models\Handler::where('target_type', \ewma\components\models\Component::class)->whereIn('target_id', table_ids($componentsBuilder->get()));
        $handlers->delete();

        $componentsBuilder->delete();
    }

    private $exportOutput;

    public function export(\ewma\components\models\Cat $cat)
    {
        $tree = \ewma\Data\Tree::get(\ewma\components\models\Cat::orderBy('position'));

        $this->exportOutput['cat_id'] = $cat->id;
        $this->exportOutput['cats'] = $tree->getFlattenData($cat->id);

        $this->exportRecursion($tree, $cat);

        return $this->exportOutput;
    }

    private function exportRecursion(\ewma\Data\Tree $tree, \ewma\components\models\Cat $cat)
    {
        $components = $cat->components()->orderBy('position')->get();
        foreach ($components as $component) {
            $this->exportOutput['components'][$cat->id][] = $this->svc->export($component);
        }

        $subcats = $tree->getSubnodes($cat->id);
        foreach ($subcats as $subcat) {
            $this->exportRecursion($tree, $subcat);
        }
    }

    public function import(\ewma\components\models\Cat $targetCat, $data, $skipFirstLevel = false)
    {
        $this->importRecursion($targetCat, $data, $data['cat_id'], $skipFirstLevel);
    }

    private function importRecursion(\ewma\components\models\Cat $targetCat, $importData, $catId, $skipFirstLevel = false)
    {
        if ($skipFirstLevel) {
            $newCat = $targetCat;
        } else {
            $newCatData = $importData['cats']['nodes_by_id'][$catId];

            if ($newCatData instanceof \Model) {
                $newCatData = $newCatData->toArray();
            }

            $newCat = $targetCat->nested()->create($newCatData);
        }

        if (!empty($importData['components'][$catId])) {
            foreach ($importData['components'][$catId] as $componentData) {
                $componentData['component']['cat_id'] = $newCat->id;

                components()->import($componentData);
            }
        }

        if (!empty($importData['cats']['ids_by_parent'][$catId])) {
            foreach ($importData['cats']['ids_by_parent'][$catId] as $sourceCatId) {
                $this->importRecursion($newCat, $importData, $sourceCatId);
            }
        }
    }
}
