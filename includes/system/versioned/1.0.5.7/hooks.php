<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class hooks {

    private $_site;
    private $_hooks = [];
    const PREFIX = 'listen_';
    private $prefix_length;
    private $pipelines = [];
    private $page;

    public function __construct($site) {
      $this->_site = basename($site);
      $this->prefix_length = strlen(self::PREFIX);
    }

    private function sort_hooks() {
      foreach ( $this->_hooks as &$groups ) {
        foreach ( $groups as &$actions ) {
          foreach ( $actions as &$codes ) {
            uksort($codes, 'strnatcmp');
          }
        }
      }
    }

    private function load($group, $alias) {
      $hooks_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT hooks_action, hooks_code, hooks_class, hooks_method
 FROM hooks
 WHERE hooks_site = '%s' AND hooks_group = '%s'
EOSQL
, tep_db_input($this->_site), tep_db_input($group)));

      while ($hook = tep_db_fetch_array($hooks_query)) {
        if ('' === $hook['hooks_class'] && function_exists($hook['hooks_method'])) {
          Guarantor::guarantee_all($this->_hooks, $this->_site, $alias, $hook['hooks_action'])[$hook['hooks_code']]
            = $hook['hooks_method'];
          continue;
        }

        if (!class_exists($hook['hooks_class'])) {
          continue;
        }

        $object = &$GLOBALS[$hook['hooks_class']];
        if (!isset($object)) {
          $object = new $hook['hooks_class']();
        }

        if (method_exists($object, $hook['hooks_method'])) {
          Guarantor::guarantee_all($this->_hooks, $this->_site, $alias, $hook['hooks_action'])[$hook['hooks_code']]
            = [$object, $hook['hooks_method']];
        }
      }

      $this->sort_hooks();
    }

    public function register($group, $alias = null) {
      $group = basename($group);
      $alias = is_null($alias) ? $group : basename($alias);
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
          $code = pathinfo($file, PATHINFO_FILENAME);
          if ( $code !== $file ) {
            $class = 'hook_' . $this->_site . '_' . $group . '_' . $code;

            $GLOBALS[$class] = new $class();

            foreach ( get_class_methods($GLOBALS[$class]) as $method ) {
              if ( substr($method, 0, $this->prefix_length) === self::PREFIX ) {
                $action = substr($method, $this->prefix_length);
                Guarantor::guarantee_all($this->_hooks, $this->_site, $alias, $action)[$code]
                  = [$GLOBALS[$class], $method];
              }
            }
          }
        }
      }

      $this->load($group, $alias);
    }

    public function register_page() {
      $this->page = pathinfo($GLOBALS['PHP_SELF'], PATHINFO_FILENAME);
      $this->register('siteWide', $this->page);
      $this->register($this->page);
    }

    public function register_pipeline($pipeline, &$parameters = null) {
      $this->pipelines[] = $pipeline;
      $this->register($pipeline, $this->page);
      $this->call($this->page, "{$pipeline}Start", $parameters);
    }

    public function call($group, $action, &$parameters = []) {
      if (('siteWide' === $group) || in_array($group, $this->pipelines)) {
        $group = $this->page;
      }

      $result = '';
      foreach ( @(array)$this->_hooks[$this->_site][$group][$action] as $callback ) {
        $result .= call_user_func($callback, $parameters);
      }

      if ( !empty($result) ) {
        return $result;
      }
    }

    public function generate($group, $action, $parameters = []) {
      foreach ( @(array)$this->_hooks[$this->_site][$group][$action] as $callback ) {
        yield call_user_func($callback, $parameters);
      }
    }

  }
