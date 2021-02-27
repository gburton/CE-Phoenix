<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class override_template extends default_template {

    public function __construct() {
      $hook_directory = DIR_FS_CATALOG . 'templates/override/includes/hooks/';
      if (is_dir($hook_directory)) {
        $this->_base_hook_directories[] = $hook_directory;
      }

      parent::__construct();
    }

    public static function _get_template_mapping_for($file, $type) {
      switch ($type) {
        case 'page':
          return DIR_FS_CATALOG . 'templates/override/includes/pages/' . basename($file);
        case 'component':
          return DIR_FS_CATALOG . 'templates/override/includes/components/' . basename($file);
        case 'module':
          $file = static::extract_relative_path($file);
          $file = dirname($file) . '/tpl_' . basename($file);

          return DIR_FS_CATALOG . "templates/override/$file";
        case 'ext':
          $file = static::extract_relative_path($file);
          return DIR_FS_CATALOG . "templates/override/includes/$file";
        case 'translation':
        case 'literal':
        default:
          $file = static::extract_relative_path($file);
          return DIR_FS_CATALOG . "templates/override/$file";
      }
    }

    public function get_template_mapping_for($file, $type) {
      $template_file = static::_get_template_mapping_for($file, $type);

      return file_exists($template_file)
           ? $template_file
           : parent::get_template_mapping_for($file, $type);
    }

  }
