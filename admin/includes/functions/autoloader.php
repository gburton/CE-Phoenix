<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  function tep_build_admin_autoload_index($modules_directory_length) {
    $class_files = [];
    
    tep_find_all_files_under(DIR_FS_ADMIN . 'includes/modules', $class_files);
    tep_find_all_files_under(DIR_FS_ADMIN . 'includes/classes', $class_files);
    
    // some classes do not follow either naming standard relating the class name and file name
    $exception_mappings = [
      'password_hash' => 'passwordhash',
      'action_recorder_admin' => 'action_recorder',
    ];

    foreach ($exception_mappings as $class_name => $filename) {
      $class_files[$class_name] = $class_files[$filename];
      unset($class_files[$filename]);
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
