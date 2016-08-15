<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link('login.php', '', 'SSL'));
  }

  require(DIR_WS_LANGUAGES . $language . '/account.php');

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('account.php', '', 'SSL'));

  require(DIR_WS_INCLUDES . 'template_top.php');
?>

<div class="page-header">
  <h1><?php echo HEADING_TITLE; ?></h1>
</div>

<?php
  if ($messageStack->size('account') > 0) {
    echo $messageStack->output('account');
  }
?>

<div class="contentContainer">
  <div class="row">

    <?php
    echo $oscTemplate->getContent('account');
    ?>
  
  </div>
</div>


<?php
  require(DIR_WS_INCLUDES . 'template_bottom.php');
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
