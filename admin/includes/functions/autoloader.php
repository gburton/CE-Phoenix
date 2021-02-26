<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  function tep_build_admin_autoload_index($modules_directory_length) {
    $class_files = [];

    tep_find_all_files_under(DIR_FS_ADMIN . 'includes/modules', $class_files);
    tep_find_all_files_under(DIR_FS_ADMIN . 'includes/classes', $class_files);

    $overrides_directory = DIR_FS_ADMIN . 'includes/classes/override';
    if (is_dir($overrides_directory)) {
      tep_find_all_files_under($overrides_directory, $class_files);
    }

    return $class_files;
  }

  function tep_autoload_admin($class) {
    static $class_files;
    static $modules_directory_length;

    if (!isset($class_files)) {
      $modules_directory_length = strlen(DIR_FS_ADMIN . 'includes/modules');
      $class_files = tep_build_admin_autoload_index($modules_directory_length);
    }

    // convert camelCase class names to snake_case filenames
    $class = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $class));

    if (isset($class_files[$class])) {
      global $language;
      if (isset($language) && DIR_FS_ADMIN . 'includes/modules' === substr($class_files[$class], 0, $modules_directory_length)) {
        $language_file = DIR_FS_ADMIN . "includes/languages/$language/modules" . substr($class_files[$class], $modules_directory_length);
        if (file_exists($language_file)) {
          include $language_file;
        }
      }

      require $class_files[$class];
    }
  }
