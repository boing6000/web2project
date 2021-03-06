<?php
/**
 * @package     web2project\modules\misc
 */

class CResource extends w2p_Core_BaseObject {
    public $resource_id = null;
    public $resource_key = null;
    public $resource_name = null;
    public $resource_type = null;
    public $resource_max_allocation = null;
    public $resource_description = null;

    public function __construct() {
        parent::__construct('resources', 'resource_id');
    }

    public function isValid()
    {
        $baseErrorMsg = get_class($this) . '::store-check failed - ';

        if ('' == trim($this->resource_name)) {
            $this->_error['resource_name'] = $baseErrorMsg . 'resource name is not set';
        }
        if ('' == trim($this->resource_key)) {
            $this->_error['resource_key'] = $baseErrorMsg . 'resource key is not set';
        }

        return (count($this->_error)) ? false : true;
    }
    /*
     * This is only here for backwards compatibility
     *
     * @deprecated
     */
    public function &loadTypes() {
        trigger_error("CResource->loadTypes() has been deprecated in v3.0 and will be removed in v4.0. Please use w2PgetSysVal('ResourceTypes') instead.", E_USER_NOTICE);

        return $this->typeSelect();
    }

    /*
     * This is only here for backwards compatibility
     *
     * @deprecated
     */
    public function typeSelect() {
        trigger_error("CResource->typeSelect() has been deprecated in v3.0 and will be removed in v4.0. Please use w2PgetSysVal('ResourceTypes') instead.", E_USER_NOTICE);

        $typelist = w2PgetSysVal('ResourceTypes');
        if (!count($typelist)) {
            include W2P_BASE_DIR . '/modules/resources/setup.php';
            $setup = new SResource();
            $setup->upgrade('1.0.1');
            $typelist = w2PgetSysVal('ResourceTypes');
        }

        return $typelist;
    }

    /*
     * This is only here for backwards compatibility
     *
     * @deprecated
     */
    public function getTypeName() {
        trigger_error("CResource->getTypeName() has been deprecated in v3.0 and will be removed in v4.0. Please use w2PgetSysVal('ResourceTypes') instead.", E_USER_NOTICE);

        $typelist = $this->typeSelect();
        return $typelist[$this->resource_type];
    }

    public function getResourcesByTask($task_id)
    {
        $q = $this->_getQuery();
        $q->addQuery('a.*');
        $q->addQuery('b.percent_allocated');
        $q->addTable('resources', 'a');
        $q->addJoin('resource_tasks', 'b', 'b.resource_id = a.resource_id', 'inner');
        $q->addWhere('b.task_id = ' . (int)$task_id);

        return $q->loadHashList('resource_id');
    }

    public function getTasksByResources($resources, $start_date, $end_date)
    {
        $q = $this->_getQuery();
        $q->addQuery('b.resource_id, sum(b.percent_allocated) as total_allocated');
        $q->addTable('tasks', 'a');
        $q->addJoin('resource_tasks', 'b', 'b.task_id = a.task_id', 'inner');
        $q->addWhere('b.resource_id IN (' . implode(',', array_keys($resources)) . ')');
        $q->addWhere("task_start_date <= '$end_date'");
        $q->addWhere("task_end_date   >= '$start_date'");
        $q->addGroup('resource_id');

        return $q->loadHashList();
    }

    public function hook_search() {
        $search['table'] = 'resources';
        $search['table_module'] = 'resources';
        $search['table_key'] = 'resource_id';
        $search['table_link'] = 'index.php?m=resources&a=view&resource_id='; // first part of link
        $search['table_title'] = 'Resources';
        $search['table_orderby'] = 'resource_name';
        $search['search_fields'] = array('resource_name', 'resource_key','resource_description');
        $search['display_fields'] = $search['search_fields'];

        return $search;
    }
}
