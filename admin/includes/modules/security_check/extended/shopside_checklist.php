<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class securityCheckExtended_shopside_checklist {
    var $type = 'info';
    var $has_doc = false;

    function __construct() {
      global $language;

      include(DIR_FS_ADMIN . 'includes/languages/' . $language . '/modules/security_check/extended/shopside_checklist.php');
      
      $this->title = MODULE_SECURITY_CHECK_EXTENDED_SHOPSIDE_CHECKLIST_TITLE;
    }

    function pass() {
      return false;
    }

    function getMessage() {
      return MODULE_SECURITY_CHECK_EXTENDED_SHOPSIDE_CHECKLIST_MESSAGE;
    }
  }
  