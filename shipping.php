<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_SHIPPING);

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_SHIPPING));

  if (file_exists('includes/templates/' . TEMPLATE . '/template_top.php') && file_exists('includes/templates/' . TEMPLATE . '/template_bottom.php')) {
	require('includes/templates/' . TEMPLATE . '/template_top.php');
		} else {
    require(DIR_WS_INCLUDES . 'template_top.php');
		}
?>

<div class="page-header">
  <h1><?php echo HEADING_TITLE; ?></h1>
</div>

<div class="contentContainer">
  <div class="contentText">
    <?php echo TEXT_INFORMATION; ?>
  </div>

  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'glyphicon glyphicon-chevron-right', tep_href_link(FILENAME_DEFAULT)); ?></div>
  </div>
</div>

<?php
  if (file_exists('includes/templates/' . TEMPLATE . '/template_bottom.php') && file_exists('includes/templates/' . TEMPLATE . '/template_top.php')) {
	require('includes/templates/' . TEMPLATE . '/template_bottom.php');
		} else {
    require(DIR_WS_INCLUDES . 'template_bottom.php');
		}
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
