<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class securityCheck_fopen_wrapper {
    var $type = 'warning';
    var $has_doc = false;

    function __construct() {
      global $language;

      include(DIR_FS_ADMIN . 'includes/languages/' . $language . '/modules/security_check/fopen_wrapper.php');

      $this->title = MODULE_SECURITY_CHECK_FOPEN_WRAPPER_TITLE;
    }

    function pass() {
      if ((int)ini_get('allow_url_fopen') == 0) return false;

      return true;
    }

    function getMessage() {
      return MODULE_SECURITY_CHECK_FOPEN_WRAPPER_ERROR;
    }
    
  }
  