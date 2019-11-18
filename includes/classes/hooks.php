<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/

  function &tep_guarantee_subarray(&$data, $key) {
    if (!isset($data[$key]) || !is_array($data[$key])) {
      $data[$key] = [];
    }

    return $data[$key];
  }

  function &tep_guarantee_all(&$data, ...$keys) {
    $current = &$data;
    foreach ($keys as $key) {
      $current = &tep_guarantee_subarray($current, $key);
    }

    return $current;
  }

  class hooks {

    public $_site;
    public $_hooks = [];
    const PREFIX = 'listen_';
    private $prefix_length;

    function __construct($site) {
      $this->_site = basename($site);
      $this->prefix_length = strlen(self::PREFIX);

      $this->register('global');
    }

    function sort_hooks() {
      foreach ( $this->_hooks as &$groups ) {
        foreach ( $groups as &$actions ) {
          foreach ( $actions as &$codes ) {
            uksort($codes, 'strnatcmp');
          }
        }
      }
    }

    function load($group) {
      $hooks_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT hooks_path, hooks_action, hooks_code, hooks_class, hooks_method
 FROM hooks
 WHERE hooks_site = '%s' AND hooks_group = '%s'
EOSQL
, tep_db_input($this->_site), tep_db_input($group)));

      while ($hook = tep_db_fetch_array($hooks_query)) {
        $file = DIR_FS_CATALOG . $hook['hooks_path'];
        if (file_exists($file) && is_readable($file)) {
          if (!class_exists($hook['hooks_class'])) {
            include($file);

            if (!class_exists($hook['hooks_class'])) {
              continue;
            }
          }

          $object = &$GLOBALS[$hook['hooks_class']];
          if (!isset($object)) {
            $object = new $hook['hooks_class']();
          }

          if (method_exists($object, $hook['hooks_method'])) {
            tep_guarantee_all($this->_hooks, $this->_site, $group, $hook['hooks_action'])[$hook['hooks_code']]
              = [$object, $hook['hooks_method']];
          }
        }
      }

      $this->sort_hooks();
    }

    function register($group) {
      $group = basename($group);

      $directory = DIR_FS_CATALOG . 'includes/hooks/' . $this->_site . '/' . $group;

      $files = [];
      
      if ( file_exists($directory) ) {
        if ( $dir = @dir($directory) ) {
          while ( $file = $dir->read() ) {
            if ( !is_dir($directory . '/' . $file) ) {
              $files[] = $file;
            }
          }

          $dir->close();
        }
        
        foreach ($files as $file) {
          $period_index = strrpos($file, '.');
          if ( substr($file, $period_index) == '.php' ) {
            $code = substr($file, 0, $period_index);
            $class = 'hook_' . $this->_site . '_' . $group . '_' . $code;

            include($directory . '/' . $file);
            $GLOBALS[$class] = new $class();

            foreach ( get_class_methods($GLOBALS[$class]) as $method ) {
              if ( substr($method, 0, $this->prefix_length) == 'listen_' ) {
                $action = substr($method, $this->prefix_length);
                tep_guarantee_all($this->_hooks, $this->_site, $group, $action)[$code]
                  = [$GLOBALS[$class], $method];
              }
            }
          }
        }
      }

      $this->load($group);
    }

    function call($group, $action, $parameters = []) {
      $result = '';

      foreach ( @(array)$this->_hooks[$this->_site][$group][$action] as $callback ) {
        $result .= call_user_func($callback, $parameters);
      }

      if ( !empty($result) ) {
        return $result;
      }
    }
  }
