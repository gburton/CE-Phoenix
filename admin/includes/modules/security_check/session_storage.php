<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class securityCheck_session_storage {

    public $type = 'warning';

    function __construct() {
      include DIR_FS_ADMIN . 'includes/languages/' . $_SESSION['language'] . '/modules/security_check/session_storage.php';
    }

    function pass() {
      return (!defined('DIR_FS_SESSION') || !DIR_FS_SESSION || (is_dir(DIR_FS_SESSION) && is_writable(DIR_FS_SESSION)));
    }

    function getMessage() {
      if (defined('DIR_FS_SESSION') && DIR_FS_SESSION) {
        if (!is_dir(DIR_FS_SESSION)) {
          return sprintf(WARNING_SESSION_DIRECTORY_NON_EXISTENT, session_save_path());
        }

        if (!is_writable(DIR_FS_SESSION)) {
          return sprintf(WARNING_SESSION_DIRECTORY_NOT_WRITEABLE, session_save_path());
        }
      }
    }

  }
