<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

// redirect the customer to a friendly cookie-must-be-enabled page if cookies are disabled (or the session has not started)
  if (!$session_started) {
    if ( !isset($_GET['cookie_test']) ) {
      $all_get = tep_get_all_get_params();

      tep_redirect(tep_href_link('login.php', (empty($all_get) ? '' : "$all_get&") . 'cookie_test=1'));
    }

    tep_redirect(tep_href_link('cookie_usage.php'));
  }

  // login content module must return $login_customer_id as an integer after successful customer authentication	
  $login_customer_id = false;
  $page_content = $oscTemplate->getContent('login');

  if ( is_int($login_customer_id) && ($login_customer_id > 0) ) {
    $OSCOM_Hooks->call('siteWide', 'postLogin');

    tep_redirect($_SESSION['navigation']->pop_snapshot_as_link());
  }

  require language::map_to_translation('login.php');
  require $oscTemplate->map_to_template(__FILE__, 'page');
  require 'includes/application_bottom.php';
