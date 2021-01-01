<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class default_template {

    protected $_grid_content_width = BOOTSTRAP_CONTENT;

    protected $_base_hook_directories = [
      DIR_FS_CATALOG . 'templates/default/includes/hooks/',
    ];

    protected $_template_mapping = [
    ];

    public function __construct() {
      $hooks =& Guarantor::ensure_global('hooks', 'shop');
      foreach ($this->_base_hook_directories as $directory) {
        $hooks->add_directory($directory);
      }

      spl_autoload_register([$this, 'autoload_hooks'], true, true);
      $GLOBALS['breadcrumb'] = new breadcrumb();
    }

    public static function extract_relative_path($file, $base_path = DIR_FS_CATALOG) {
      if ('/' !== DIRECTORY_SEPARATOR) {
        $file = str_replace(DIRECTORY_SEPARATOR, '/', $file);
      }

      return Text::ltrim_once($file, $base_path);
    }

    public static function _get_template_mapping_for($file, $type) {
      switch ($type) {
        case 'page':
          return DIR_FS_CATALOG . 'templates/default/includes/pages/' . basename($file);
        case 'component':
          return DIR_FS_CATALOG . 'templates/default/includes/components/' . basename($file);
        case 'module':
          return dirname($file) . '/templates/tpl_' . basename($file);
        case 'ext':
          $file = static::extract_relative_path($file);
          return DIR_FS_CATALOG . "templates/default/includes/$file";
        case 'translation':
          return DIR_FS_CATALOG . $file;
        case 'literal':
        default:
          return DIR_FS_CATALOG . "templates/default/$file";
      }
    }

    public function get_template_mapping_for($file, $type) {
      $template_file = $this->_template_mapping[$file]
                    ?? static::_get_template_mapping_for($file, $type);

      return file_exists($template_file) ? $template_file : null;
    }

    public function autoload_hooks($requested_class) {
      static $class_files;

      if (is_null($class_files)) {
        $class_files = [];
        foreach ($this->_base_hook_directories as $directory) {
          tep_find_all_hooks_under($directory, $class_files);
        }
      }

      $class = tep_normalize_class_name($requested_class);

      if (isset($class_files[$class])) {
        require $class_files[$class];
      }
    }

    public function setGridContentWidth($width) {
      $this->_grid_content_width = $width;
    }

    public function getGridContentWidth() {
      return $this->_grid_content_width;
    }

    public function getGridColumnWidth() {
      return (12 - BOOTSTRAP_CONTENT) / 2;
    }

  }
