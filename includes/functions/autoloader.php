<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  function tep_find_all_files_under($directory, &$files) {
    $current_entries = scandir($directory, SCANDIR_SORT_ASCENDING);

    foreach ($current_entries as $entry) {
      // we have no file or directory names starting with a dot
      // so it's safe to screen out anything that does, like the current and parent directories
      if ('.' === $entry[0]) {
        continue;
      }

      $path = "$directory/$entry";
      if (is_file($path)) {
        $files[pathinfo($entry, PATHINFO_FILENAME)] = $path;
      } elseif (is_dir($path) && 'cc_braintree' != $entry && 'templates' != $entry) {
        // templates directories are underneath the modules directory but do not contain classes
        // cc_braintree uses its own naming convention, so we make it load its own classes
        tep_find_all_files_under($path, $files);
      }
    }
  }

  function tep_build_catalog_autoload_index() {
    $class_files = [];

    tep_find_all_files_under(DIR_FS_CATALOG . 'includes/hooks', $class_files);
    tep_find_all_files_under(DIR_FS_CATALOG . 'includes/modules', $class_files);
    tep_find_all_files_under(DIR_FS_CATALOG . 'includes/classes', $class_files);
    tep_find_all_files_under(DIR_FS_CATALOG . 'includes/system/versioned', $class_files);

    // some classes do not follow either naming standard relating the class name and file name
    $exception_mappings = [
      'alert_block' => 'alertbox',
      'os_c__actions' => 'actions',
      'm_c_a_p_i' => 'MCAPI.class',
      'password_hash' => 'passwordhash',
    ];

    foreach ($exception_mappings as $class_name => $filename) {
      $class_files[$class_name] = $class_files[$filename];
      unset($class_files[$filename]);
    }

    return $class_files;
  }

  function tep_autoload_catalog($class) {
    static $class_files;
    static $modules_directory_length;

    if (!isset($class_files)) {
      $modules_directory_length = strlen(DIR_FS_CATALOG . 'includes/modules');
      $class_files = tep_build_catalog_autoload_index();
    }

    // convert camelCase class names to snake_case filenames
    $class = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $class));

    if (isset($class_files[$class])) {
      if (isset($GLOBALS['language']) && DIR_FS_CATALOG . 'includes/modules' === substr($class_files[$class], 0, $modules_directory_length)) {
        $language_file = DIR_FS_CATALOG . 'includes/languages/'. $GLOBALS['language'] . '/modules' . substr($class_files[$class], $modules_directory_length);
        if (file_exists($language_file)) {
          include $language_file;
        }
      }

      require $class_files[$class];
    }
  }

