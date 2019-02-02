<?php namespace ewma\components\ui\controllers\main;

class Cats extends \Controller
{
    public function reload()
    {
        $this->jquery()->replace($this->view());
    }

    public function view()
    {
        $v = $this->v();

        $s = $this->s('~');

        $selectedCatId = $s['selected_cat_id'];

        $rootNode = components()->cats->getRootCat();

        $v->assign([
                       'CONTENT' => $this->c('\std\ui\tree~:view|' . $this->_nodeId(), [
                           'default'          => [
                               'query_builder' => '>app:getQueryBuilder'
                           ],
                           'node_control'     => [
                               '>node:view',
                               [
                                   'cat'         => '%model',
                                   'root_cat_id' => $rootNode->id
                               ]
                           ],
                           'callbacks'        => [
                               'move' => $this->_abs('>app:moveCallback', [
                                   'cat' => '%source_model'
                               ]),
                               'sort' => $this->_abs('>app:sortCallback', [
                                   'cat' => '%parent_model'
                               ])
                           ],
                           'root_node_id'     => $rootNode->id,
                           'selected_node_id' => $selectedCatId,
                           'movable'          => true,
                           'sortable'         => true,
                           'droppable'        => [
                               'component' => [
                                   'accept'         => $this->_selector('@components:. .component'),
                                   'source_id_attr' => 'component_id',
                                   'path'           => 'component~app:setCat',
                                   'data'           => [
                                       'cat_id'       => '%target_id',
                                       'component_id' => '%source_id'
                                   ]
                               ]
                           ],
                           'permissions'      => $this->_module()->namespace . ':~'
                       ])
                   ]);

        $this->css();

        $this->e('ewma/components/cat_select')->rebind(':reload');

        $this->e('ewma/components/cats/create')->rebind(':reload');
        $this->e('ewma/components/cats/delete')->rebind(':reload');
        $this->e('ewma/components/cats/import')->rebind(':reload');

        return $v;
    }
}
