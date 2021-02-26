<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class securityCheck_download_directory {

    const FS_DOWNLOAD_DIRECTORY = DIR_FS_CATALOG . 'download/';

    public $type = 'warning';

    function __construct() {
      include DIR_FS_ADMIN . 'includes/languages/' . $_SESSION['language'] . '/modules/security_check/download_directory.php';
    }

    function pass() {
      return ('true' !== DOWNLOAD_ENABLED) || is_dir(static::FS_DOWNLOAD_DIRECTORY);
    }

    function getMessage() {
      return sprintf(WARNING_DOWNLOAD_DIRECTORY_NON_EXISTENT, static::FS_DOWNLOAD_DIRECTORY);
    }

  }
