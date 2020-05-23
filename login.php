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

      tep_redirect(tep_href_link('login.php', (empty($all_get) ? '' : "$all_get&") . 'cookie_test=1', 'SSL'));
    }

    tep_redirect(tep_href_link('cookie_usage.php'));
  }

  // login content module must return $login_customer_id as an integer after successful customer authentication	
  $login_customer_id = false;
  $page_content = $oscTemplate->getContent('login');

  if ( is_int($login_customer_id) && ($login_customer_id > 0) ) {
    $OSCOM_Hooks->call('siteWide', 'postLogin');
    if (SESSION_RECREATE == 'True') {
      tep_session_recreate();
    }

    $_SESSION['customer_id'] = $login_customer_id;

    tep_db_query("UPDATE customers_info SET customers_info_date_of_last_logon = NOW(), customers_info_number_of_logons = customers_info_number_of_logons+1, password_reset_key = null, password_reset_date = null WHERE customers_info_id = " . (int)$_SESSION['customer_id']);

    tep_reset_session_token();
    $_SESSION['cart']->restore_contents();

    tep_redirect($_SESSION['navigation']->pop_snapshot_as_link());
  }

  require "includes/languages/$language/login.php";

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('login.php', '', 'SSL'));

  require 'includes/template_top.php';

  if ($messageStack->size('login') > 0) {
    echo $messageStack->output('login');
  }
?>

  <div class="row">
    <?php echo $page_content; ?>
  </div>

<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
