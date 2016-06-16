<?php
/*

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2016 osCommerce

  Released under the GNU General Public License

*/
?>
    <div class="buttonSet col-sm-<?php echo (int)MODULE_CONTENT_ACCOUNT_HISTORY_INFO_BUTTON_BACK_CONTENT_WIDTH; ?>">
    <?php echo tep_draw_button(IMAGE_BUTTON_BACK, 'fa fa-angle-left', tep_href_link(FILENAME_ACCOUNT_HISTORY, tep_get_all_get_params(array('order_id')), 'SSL')); ?>
    </div>
