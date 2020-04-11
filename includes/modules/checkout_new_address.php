<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/
?>

  <div class="contentText">

<?php
  if (!isset($customer_details)) {
    $customer_details = null;
  }
  $customer_data->display_input($customer_data->get_fields_for_page('checkout_new_address'), $customer_details);
?>

</div>
