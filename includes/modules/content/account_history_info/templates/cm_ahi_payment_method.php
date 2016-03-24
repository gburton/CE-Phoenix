<?php
/*

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2016 osCommerce

  Released under the GNU General Public License

*/
?>
<!-- Start cm_ahi_payment_method module -->
      <div class="col-sm-<?php echo (int)MODULE_CONTENT_ACCOUNT_HISTORY_INFO_PAYMENT_METHOD_CONTENT_WIDTH; ?>">
        <div class="panel panel-info">
          <div class="panel-heading"><?php echo '<strong>' . HEADING_PAYMENT_METHOD . '</strong>'; ?></div>
          <div class="panel-body">
          <?php echo $order->info['payment_method']; ?>
          </div>
        </div>
      </div>
<!-- End cm_ahi_payment_method module -->
