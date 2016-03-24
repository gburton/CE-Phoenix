<?php
/*

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2016 osCommerce

  Released under the GNU General Public License

*/
?>
<!-- Start cm_ahi_billing module -->
      <div class="col-sm-<?php echo (int)MODULE_CONTENT_ACCOUNT_HISTORY_INFO_BILLING_CONTENT_WIDTH; ?>">
        <div class="panel panel-warning">
          <div class="panel-heading"><?php echo '<strong>' . HEADING_BILLING_ADDRESS . '</strong>'; ?></div>
          <div class="panel-body">
            <?php echo tep_address_format($order->billing['format_id'], $order->billing, 1, ' ', '<br />'); ?>
          </div>
        </div>
      </div>
<!-- End cm_ahi_billing module -->
