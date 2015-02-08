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
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_ACCOUNT);

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));

  if (file_exists('includes/templates/' . TEMPLATE . '/template_top.php') && file_exists('includes/templates/' . TEMPLATE . '/template_bottom.php')) {
	require('includes/templates/' . TEMPLATE . '/template_top.php');
		} else {
    require(DIR_WS_INCLUDES . 'template_top.php');
		}
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

<?php
  echo $oscTemplate->getContent('account');
?>

</div>


<?php
  if (file_exists('includes/templates/' . TEMPLATE . '/template_bottom.php') && file_exists('includes/templates/' . TEMPLATE . '/template_top.php')) {
	require('includes/templates/' . TEMPLATE . '/template_bottom.php');
		} else {
    require(DIR_WS_INCLUDES . 'template_bottom.php');
		}
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
