<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/
?>

<div class="row">
  <?php echo $oscTemplate->getContent('header'); ?>
</div>

<div class="body-sans-header">

<?php
  if (isset($_GET['error_message']) && tep_not_null($_GET['error_message'])) {
?>
  <div class="alert alert-danger" role="alert">
    <a href="#" class="close fas fa-times" data-dismiss="alert"></a>
    <?php echo htmlspecialchars(stripslashes(urldecode($_GET['error_message']))); ?>
  </div>
<?php
  }

  if (isset($_GET['info_message']) && tep_not_null($_GET['info_message'])) {
?>
  <div class="alert alert-info" role="alert">
    <a href="#" class="close fas fa-times" data-dismiss="alert"></a>
    <?php echo htmlspecialchars(stripslashes(urldecode($_GET['info_message']))); ?>
  </div>
<?php
  }
?>
