<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2020 osCommerce; http://www.oscommerce.com
 * @license GNU General Public License; http://www.oscommerce.com/gpllicense.txt
 */

  abstract class Tree {

    protected static $_parents = [];

    protected $_data = [];
    protected $root_id = 0;

    public function exists($id) {
      return isset(static::$_parents[$id]);
    }

    public function get_children($id) {
      return array_keys($this->_data[$id] ?? []);
    }

    public function get_descendants($id, &$results = []) {
      foreach ($this->get_children($id) as $child_id) {
        $results[] = $child_id;
        $this->get_descendants($child_id, $results);
      }

      return $results;
    }

    public function get_ancestors($id, &$results = []) {
      while (($id = $this->get_parent_id($id)) && ($id != $this->root_id)) {
        $results[] = $id;
      }

      return $results;
    }

    public function find_path($id, $glue = '_') {
      $nodes = array_reverse($this->get_ancestors($id));
      $nodes[] = $id;

      return implode($glue, $nodes);
    }

    public function parse_path($path) {
      return array_unique(array_map('intval', explode('_', $path)), SORT_NUMERIC);
    }

/**
 * Return node information
 *
 * @param int $id Return information for this node ID
 * @param string $key The key information to return
 * @return mixed
 */
    public function get($id, $key = null) {
      if ( isset(static::$_parents[$id]) ) {
        $data = $this->_data[static::$_parents[$id]][$id];

        $data['id'] = $id;
        $data['parent_id'] = static::$_parents[$id];

        return ( is_null($key) ? $data : $data[$key] );
      }

      return null;
    }

/**
 * Return the parent ID of a node
 *
 * @param int $id Return the parent ID of this ID
 * @return int
 */
    public function get_parent_id($id) {
      return static::$_parents[$id] ?? null;
    }

    public function get_root_id() {
      return $this->root_id;
    }

    public function set_root_id($root_id) {
      $this->root_id = $root_id;
    }

/**
 * Return a formated string representation of the category structure relationship data
 *
 * @access public
 * @return string
 */
    public function getTree() {
      $display = new tree_display($this);

      return $display->_buildBranch($this->root_id);
    }

/**
 * Magic function; return a formated string representation of the category structure relationship data
 *
 * This is used when echoing the class object, eg:
 *
 * echo $tree;
 *
 * @access public
 * @return string
 */
    public function __toString() {
      return $this->getTree();
    }

  }
