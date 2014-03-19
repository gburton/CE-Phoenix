<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

?>

<p align="center"><?php echo FOOTER_TEXT_BODY; ?></p>

<?php
  if ($banner = tep_banner_exists('dynamic', 'footer')) {
?>

<p style="text-align: center; margin-bottom: 20px;">
  <?php echo tep_display_banner('static', $banner); ?>
</p>

<?php
  }
?>

