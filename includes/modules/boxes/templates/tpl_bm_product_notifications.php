<div class="card mb-2 bm-product-notifications">
  <div class="card-header"><?php echo MODULE_BOXES_PRODUCT_NOTIFICATIONS_BOX_TITLE; ?></div>
  <div class="list-group list-group-flush">
    <?php
  if ($notification_exists) {
    echo '<a class="list-group-item list-group-item-action" href="' . tep_href_link($PHP_SELF, tep_get_all_get_params(['action']) . 'action=notify_remove', $request_type) . '"><i class="fas fa-times"></i> ' . sprintf(MODULE_BOXES_PRODUCT_NOTIFICATIONS_BOX_NOTIFY_REMOVE, tep_get_products_name($_GET['products_id'])) .'</a>';
  } else {
    echo '<a class="list-group-item list-group-item-action" href="' . tep_href_link($PHP_SELF, tep_get_all_get_params(['action']) . 'action=notify', $request_type) . '"><i class="fas fa-envelope"></i> ' . sprintf(MODULE_BOXES_PRODUCT_NOTIFICATIONS_BOX_NOTIFY, tep_get_products_name($_GET['products_id'])) .'</a>';
  } ?>
  </div>
  <div class="card-footer"><a class="card-link" href="<?php echo tep_href_link('account_notifications.php', '', 'SSL'); ?>"><?php echo MODULE_BOXES_PRODUCT_NOTIFICATIONS_VIEW; ?></a></div>
</div>

<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/
?>