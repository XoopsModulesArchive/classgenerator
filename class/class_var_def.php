<?php

// class_var_def.php,v 1
//  ---------------------------------------------------------------- //
// Author: Bruno Barthez	                                           //
// ----------------------------------------------------------------- //

require_once XOOPS_ROOT_PATH . '/kernel/object.php';
/**
 * class_var_def class.
 * $this class is responsible for providing data access mechanisms to the data source
 * of XOOPS user class objects.
 */
class class_var_def extends XoopsObject
{
    public $db;

    // constructor

    public function __construct($id = null)
    {
        $this->db = XoopsDatabaseFactory::getDatabaseConnection();

        $this->initVar('var_id', XOBJ_DTYPE_INT, null, false, 10);

        $this->initVar('class_id', XOBJ_DTYPE_INT, null, false, 10);

        $this->initVar('var_name', XOBJ_DTYPE_TXTBOX, null, false);

        $this->initVar('is_show', XOBJ_DTYPE_INT, null, false, 10);

        $this->initVar('date_creation', XOBJ_DTYPE_TXTBOX, null, false);

        $this->initVar('date_last_update', XOBJ_DTYPE_TXTBOX, null, false);

        if (!empty($id)) {
            if (is_array($id)) {
                $this->assignVars($id);
            } else {
                $this->load((int)$id);
            }
        } else {
            $this->setNew();
        }
    }

    public function load($id)
    {
        $sql = 'SELECT * FROM ' . $this->db->prefix('class_var_def') . ' WHERE var_id=' . $id;

        $myrow = $this->db->fetchArray($this->db->query($sql));

        $this->assignVars($myrow);

        if (!$myrow) {
            $this->setNew();
        }
    }

    public function getAllclass_var_defs($criteria = [], $asobject = false, $sort = 'var_id', $order = 'ASC', $limit = 0, $start = 0)
    {
        $db = XoopsDatabaseFactory::getDatabaseConnection();

        $ret = [];

        $where_query = '';

        if (is_array($criteria) && count($criteria) > 0) {
            $where_query = ' WHERE';

            foreach ($criteria as $c) {
                $where_query .= " $c AND";
            }

            $where_query = mb_substr($where_query, 0, -4);
        } elseif (!is_array($criteria) && $criteria) {
            $where_query = ' WHERE ' . $criteria;
        }

        if (!$asobject) {
            $sql = 'SELECT var_id FROM ' . $db->prefix('class_var_def') . "$where_query ORDER BY $sort $order";

            $result = $db->query($sql, $limit, $start);

            while ($myrow = $db->fetchArray($result)) {
                $ret[] = $myrow['class_var_def_id'];
            }
        } else {
            $sql = 'SELECT * FROM ' . $db->prefix('class_var_def') . "$where_query ORDER BY $sort $order";

            $result = $db->query($sql, $limit, $start);

            while ($myrow = $db->fetchArray($result)) {
                $ret[] = new self($myrow);
            }
        }

        return $ret;
    }
}
// -------------------------------------------------------------------------
// ------------------class_var_def user handler class -------------------
// -------------------------------------------------------------------------
/**
 * class_var_defhandler class.
 * This class provides simple mecanisme for class_var_def object
 */
class class_var_defHandler extends XoopsObjectHandler
{
    /**
     * create a new class_var_def
     *
     * @param bool $isNew flag the new objects as "new"?
     * @return object class_var_def
     */
    public function &create($isNew = true)
    {
        $class_var_def = new class_var_def();

        if ($isNew) {
            $class_var_def->setNew();
        }

        return $class_var_def;
    }

    /**
     * retrieve a class_var_def
     *
     * @param int $id of the class_var_def
     * @return mixed reference to the {@link class_var_def} object, FALSE if failed
     */
    public function get($id)
    {
        $sql = 'SELECT * FROM ' . $this->db->prefix('class_var_def') . ' WHERE var_id=' . $id;

        if (!$result = $this->db->query($sql)) {
            return false;
        }

        $numrows = $this->db->getRowsNum($result);

        if (1 == $numrows) {
            $class_var_def = new class_var_def();

            $class_var_def->assignVars($this->db->fetchArray($result));

            return $class_var_def;
        }

        return false;
    }

    /**
     * insert a new class_var_def in the database
     *
     * @param \XoopsObject $class_var_def reference to the {@link class_var_def} object
     * @param bool         $force
     * @return bool FALSE if failed, TRUE if already present and unchanged or successful
     */
    public function insert(XoopsObject $class_var_def, $force = false)
    {
        global $xoopsConfig;

        if ('class_var_def' != get_class($class_var_def)) {
            return false;
        }

        if (!$class_var_def->isDirty()) {
            return true;
        }

        if (!$class_var_def->cleanVars()) {
            return false;
        }

        foreach ($class_var_def->cleanVars as $k => $v) {
            ${$k} = $v;
        }

        $now = 'date_add(now(), interval ' . $xoopsConfig['server_TZ'] . ' hour)';

        if ($class_var_def->isNew()) {
            // ajout/modification d'un class_var_def

            $class_var_def = new class_var_def();

            $format = 'INSERT INTO %s (var_id, class_id, var_name, is_show, date_creation, date_last_update)';

            $format .= 'VALUES (%u, %u, %s, %u, %s, %s)';

            $sql = sprintf(
                $format,
                $this->db->prefix('class_var_def'),
                $var_id,
                $class_id,
                $this->db->quoteString($var_name),
                $is_show,
                $this->db->quoteString($date_creation),
                $this->db->quoteString($date_last_update)
            );

            $force = true;
        } else {
            $format = 'UPDATE %s SET ';

            $format .= 'var_id=%u, class_id=%u, var_name=%s, is_show=%u, date_creation=%s, date_last_update=%s';

            $format .= ' WHERE var_id = %u';

            $sql = sprintf(
                $format,
                $this->db->prefix('class_var_def'),
                $var_id,
                $class_id,
                $this->db->quoteString($var_name),
                $is_show,
                $this->db->quoteString($date_creation),
                $this->db->quoteString($date_last_update),
                $var_id
            );
        }

        if (false !== $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }

        if (!$result) {
            return false;
        }

        if (empty($var_id)) {
            $var_id = $this->db->getInsertId();
        }

        $class_var_def->assignVar('var_id', $var_id);

        return true;
    }

    /**
     * delete a class_var_def from the database
     *
     * @param \XoopsObject $class_var_def reference to the class_var_def to delete
     * @param bool         $force
     * @return bool FALSE if failed.
     */
    public function delete(XoopsObject $class_var_def, $force = false)
    {
        if ('class_var_def' != get_class($class_var_def)) {
            return false;
        }

        $sql = sprintf('DELETE FROM %s WHERE var_id = %u', $this->db->prefix('class_var_def'), $class_var_def->getVar('var_id'));

        if (false !== $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }

        if (!$result) {
            return false;
        }

        return true;
    }

    /**
     * retrieve class_var_defs from the database
     *
     * @param null $criteria  {@link CriteriaElement} conditions to be met
     * @param bool $id_as_key use the UID as key for the array?
     * @return array array of {@link class_var_def} objects
     */
    public function &getObjects($criteria = null, $id_as_key = false)
    {
        $ret = [];

        $limit = $start = 0;

        $sql = 'SELECT * FROM ' . $this->db->prefix('class_var_def');

        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();

            if ('' != $criteria->getSort()) {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }

            $limit = $criteria->getLimit();

            $start = $criteria->getStart();
        }

        $result = $this->db->query($sql, $limit, $start);

        if (!$result) {
            return $ret;
        }

        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $class_var_def = new class_var_def();

            $class_var_def->assignVars($myrow);

            if (!$id_as_key) {
                $ret[] = &$class_var_def;
            } else {
                $ret[$myrow['var_id']] = &$class_var_def;
            }

            unset($class_var_def);
        }

        return $ret;
    }

    /**
     * count class_var_defs matching a condition
     *
     * @param null $criteria {@link CriteriaElement} to match
     * @return int count of class_var_defs
     */
    public function getCount($criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix('class_var_def');

        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }

        $result = $this->db->query($sql);

        if (!$result) {
            return 0;
        }

        [$count] = $this->db->fetchRow($result);

        return $count;
    }

    /**
     * delete class_var_defs matching a set of conditions
     *
     * @param null $criteria {@link CriteriaElement}
     * @return bool FALSE if deletion failed
     */
    public function deleteAll($criteria = null)
    {
        $sql = 'DELETE FROM ' . $this->db->prefix('class_var_def');

        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }

        if (!$result = $this->db->query($sql)) {
            return false;
        }

        return true;
    }
}
