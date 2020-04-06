<div class="card mb-2 bm-order-history">
  <div class="card-header"><?php echo MODULE_BOXES_ORDER_HISTORY_BOX_TITLE; ?></div>
  <ul class="list-group list-group-flush">
    <?php
  while ($products = tep_db_fetch_array($products_query)) {
    echo '<li class="list-group-item d-flex justify-content-between align-items-center"><a href="' . tep_href_link('product_info.php', 'products_id=' . $products['products_id']) . '">' . $products['products_name'] . '</a><span class="badge"><a class="badge badge-primary" href="' . tep_href_link($PHP_SELF, tep_get_all_get_params(['action']) . 'action=cust_order&pid=' . $products['products_id']) . '"><i class="fas fa-shopping-cart fa-fw fa-2x"></i></a></span></li>';
  } ?>
  </ul>
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
