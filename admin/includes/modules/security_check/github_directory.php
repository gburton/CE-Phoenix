<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class securityCheck_github_directory {
    var $type = 'warning';

    function __construct() {
      global $language;

      include(DIR_FS_ADMIN . 'includes/languages/' . $language . '/modules/security_check/github_directory.php');
    
      $this->title = MODULE_SECURITY_CHECK_GITHUB_TITLE;
    }

    function pass() {
      return !file_exists(DIR_FS_CATALOG . '.github');
    }

    function getMessage() {
      return MODULE_SECURITY_CHECK_GITHUB_DIRECTORY_EXISTS;
    }
  }
  