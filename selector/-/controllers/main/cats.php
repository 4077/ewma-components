<?php namespace ewma\components\selector\controllers\main;

class Cats extends \Controller
{
    public function view()
    {
        $v = $this->v();

        $rootNode = components()->cats->getRootCat();

        $s = $this->s('~|');

        $selectedCatId = $s['selected_cat_id'];

        $v->assign([
                       'CONTENT' => $this->c('\std\ui\tree~:view|' . $this->_nodeId(), [
                           'query_builder'     => $this->_abs('>app:getQueryBuilder'),
                           'node_control'      => [
                               '>node:view|',
                               [
                                   'cat'         => '%model',
                                   'root_cat_id' => $rootNode->id
                               ]
                           ],
                           'root_node_id'      => $rootNode->id,
                           'root_node_visible' => false,
                           'selected_node_id'  => $selectedCatId,
                           'filter_ids'        => $s['available_cats_ids']
                       ])
                   ]);

        $this->css();

        return $v;
    }
}
