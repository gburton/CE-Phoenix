<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class cfg_modules {

    var $_modules = [];

    function __construct() {
      $file_extension = pathinfo($GLOBALS['PHP_SELF'], PATHINFO_EXTENSION);
      $directory = 'includes/modules/cfg_modules';

      if ($dir = @dir($directory)) {
        while ($file = $dir->read()) {
          if (!is_dir("$directory/$file") && pathinfo($file, PATHINFO_EXTENSION) === $file_extension) {
            $class = pathinfo($file, PATHINFO_FILENAME);
            $m = new $class();

            $this->_modules[] = [
              'code' => $m->code,
              'directory' => $m->directory,
              'language_directory' => $m->language_directory,
              'key' => $m->key,
              'title' => $m->title,
              'template_integration' => $m->template_integration,
            ];
          }
        }
      }
    }

    function getAll() {
      return $this->_modules;
    }

    function get($code, $key) {
      foreach ($this->_modules as $m) {
        if ($m['code'] == $code) {
          return $m[$key];
        }
      }
    }

    function exists($code) {
      foreach ($this->_modules as $m) {
        if ($m['code'] == $code) {
          return true;
        }
      }

      return false;
    }

    public static function can($module, $action) {
      if (method_exists($module, 'can')) {
        return $module->can($action);
      }

      switch ($action) {
        case 'install':
          $requirements = get_class($module) . '::REQUIRES';
          if (!defined($requirements) || empty($requirements = constant($requirements)) || !is_array($requirements)) {
            return true;
          }

          return $GLOBALS['customer_data']->has($requirements);
        case 'remove':
          $provides = get_class($module) . '::PROVIDES';
          echo "$provides<br />\n";
          if (!defined($provides)) {
            return true;
          }
          $provides = constant($provides);

          if (empty($provides) || !is_array($provides)) {
            $provides = [];
          }

          // we can remove if nothing requires this module's abilities
          return !$GLOBALS['customer_data']->has_requirements($provides, get_class($module));
        default:
          return true;
      }
    }

  }
