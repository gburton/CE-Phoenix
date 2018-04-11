<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2012 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

// redirect the customer to a friendly cookie-must-be-enabled page if cookies are disabled (or the session has not started)
  if ($session_started == false) {
    if ( !isset($_GET['cookie_test']) ) {
      $all_get = tep_get_all_get_params();

      tep_redirect(tep_href_link('login.php', $all_get . (empty($all_get) ? '' : '&') . 'cookie_test=1', 'SSL'));
    }

    tep_redirect(tep_href_link('cookie_usage.php'));
  }
  
  $page_content = $oscTemplate->getContent('login');

  require('includes/languages/' . $language . '/login.php');

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('login.php', '', 'SSL'));

  require('includes/template_top.php');
?>

<?php
  if ($messageStack->size('login') > 0) {
    echo $messageStack->output('login');
  }
?>

<div class="contentContainer">
  <div class="row">
    <?php echo $page_content; ?>
  </div>
</div>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
